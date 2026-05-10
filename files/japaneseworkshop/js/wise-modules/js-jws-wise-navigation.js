const naviCreateButton = document.getElementById('naviCreateNewButton');


let wise_navi_status = window.wise_navi_status || 'wait';
let wise_navi_waypoint_index = INDEX_FIRST;
let wise_navi_waypoint_length = COUNT_EMPTY;
let wise_navi_script_index = INDEX_FIRST;
let wise_navi_script_length = COUNT_EMPTY;
let wiseNavigationSequence = [];
const cloneMap = new WeakMap();
let currentScanTargets = [];
let openingIndex = -1;
let endingIndex = -1;
let autoClickInterval = null;



const languages = ["ja-JP", "zh-TW", "en-US"];

const tagLangMap = {
    jpn: "ja-JP",
    cht: "zh-TW",
    eng: "en-US",
};

const languageTags = [
    ['[jpn]','[/jpn]'],
    ['[cht]','[/cht]'],
    ['[eng]','[/eng]']
];

const speechReplacements = [
    { pattern: /\bW\.I\.S\.E\.\b/gi, replace: "wise" }
];


let __speakBusy = false;
const SCAN_MIN_FREQ = 120;
const SCAN_MAX_FREQ = 1600;
const SCAN_MAX_RAMP_MS = 10000;

(() => {
    class SoundFX {
        constructor() {
            this.ctx = null;
            this.master = null;
            this.unlocked = false;

            const unlock = () => {
                if (this.unlocked) return;
                this.ctx = new (window.AudioContext || window.webkitAudioContext)();
                this.master = this.ctx.createGain();
                this.master.gain.value = 0.7;
                this.master.connect(this.ctx.destination);
                this.unlocked = true;
                window.removeEventListener("pointerdown", unlock);
                window.removeEventListener("keydown", unlock);
            };

            window.addEventListener("pointerdown", unlock, { once: true });
            window.addEventListener("keydown", unlock, { once: true });
        }

        _tone(params = {}, post = null) {
            if (!this.unlocked) return;
            const {
                type = "sine",
                freq = 440,
                duration = 0.15,
                attack = 0.005,
                decay = 0.08,
                release = 0.04,
                gain = 0.6
            } = params;

            const t0 = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const g = this.ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, t0);

            g.gain.setValueAtTime(0.0001, t0);
            g.gain.linearRampToValueAtTime(gain, t0 + attack);
            g.gain.linearRampToValueAtTime(gain * 0.7, t0 + attack + decay);
            g.gain.exponentialRampToValueAtTime(0.0001, t0 + duration - release);

            let node = g;
            if (typeof post === "function") {
                node = post(g, t0, duration) || g;
            }

            osc.connect(g);
            node.connect(this.master);

            osc.start(t0);
            osc.stop(t0 + duration);
        }

		startScanHold({ minFreq = SCAN_MIN_FREQ, maxFreq = SCAN_MAX_FREQ, rampMs = SCAN_MAX_RAMP_MS, vol = 0.55, type = "sawtooth", subLevel = 0.6 } = {}) {
			if (!this.unlocked) return;
			if (this._scanGain) this.stopScanLoop(0);
			const t0 = this.ctx.currentTime;
			const ramp = Math.min(Math.max(0, rampMs), SCAN_MAX_RAMP_MS) / 1000;
			const f0 = Math.max(1, minFreq);
			const f1 = Math.max(f0 + 1, maxFreq);

			const osc = this.ctx.createOscillator();
			const sub = this.ctx.createOscillator();
			const subGain = this.ctx.createGain();
			const g = this.ctx.createGain();
			const bp = this.ctx.createBiquadFilter();
			const shelf = this.ctx.createBiquadFilter();

			osc.type = type;
			sub.type = "sine";

			bp.type = "bandpass";
			bp.Q.value = 5.5;

			shelf.type = "lowshelf";
			shelf.frequency.setValueAtTime(160, t0);
			shelf.gain.setValueAtTime(8, t0);

			g.gain.setValueAtTime(0.0001, t0);
			g.gain.linearRampToValueAtTime(vol, t0 + 0.05);

			osc.frequency.setValueAtTime(f0, t0);
			osc.frequency.exponentialRampToValueAtTime(f1, t0 + ramp);

			subGain.gain.setValueAtTime(subLevel, t0);
			sub.frequency.setValueAtTime(f0 / 2, t0);
			sub.frequency.exponentialRampToValueAtTime(f1 / 2, t0 + ramp);

			bp.frequency.setValueAtTime(f0 * 1.05, t0);
			bp.frequency.exponentialRampToValueAtTime(f1 * 0.95, t0 + ramp);

			osc.connect(bp);
			bp.connect(shelf);
			sub.connect(subGain);
			subGain.connect(shelf);
			shelf.connect(g);
			g.connect(this.master);

			osc.start(t0);
			sub.start(t0);

			this._scanGain = g;
			this._scanOsc = osc;
			this._scanSub = sub;
			this._scanBp = bp;
			this._scanShelf = shelf;
		}

		stopScanLoop(fadeMs = 200) {
			if (!this._scanGain) return;
			const t = this.ctx.currentTime;
			const fade = Math.max(0, fadeMs) / 1000;

			this._scanGain.gain.cancelScheduledValues(t);
			this._scanGain.gain.setValueAtTime(this._scanGain.gain.value, t);
			this._scanGain.gain.exponentialRampToValueAtTime(0.0001, t + fade);

			const stopAt = t + fade + 0.05;

			if (this._scanOsc) this._scanOsc.stop(stopAt);
			if (this._scanSub) this._scanSub.stop(stopAt);

			setTimeout(() => {
				if (this._scanOsc) this._scanOsc.disconnect();
				if (this._scanSub) this._scanSub.disconnect();
				if (this._scanBp) this._scanBp.disconnect();
				if (this._scanShelf) this._scanShelf.disconnect();
				if (this._scanGain) this._scanGain.disconnect();
				this._scanGain = null;
				this._scanOsc = null;
				this._scanSub = null;
				this._scanBp = null;
				this._scanShelf = null;
			}, Math.ceil((fade + 0.1) * 1000));
		}

		hit({ ms = 180, start = 1100, end = 1700, vol = 0.55, type = "triangle" } = {}) {
			if (!this.unlocked) return;
			const now = this.ctx.currentTime;
			if (this._hitUntil && now < this._hitUntil) return;

			const dur = ms / 1000;
			const t0 = now;
			const osc = this.ctx.createOscillator();
			const g = this.ctx.createGain();
			const bp = this.ctx.createBiquadFilter();

			osc.type = type;
			osc.frequency.setValueAtTime(Math.max(1, start), t0);
			osc.frequency.exponentialRampToValueAtTime(Math.max(1.001, end), t0 + dur * 0.6);

			bp.type = "bandpass";
			bp.Q.value = 7;
			bp.frequency.setValueAtTime(start * 1.1, t0);
			bp.frequency.exponentialRampToValueAtTime(end * 0.9, t0 + dur * 0.6);

			g.gain.setValueAtTime(0.0001, t0);
			g.gain.linearRampToValueAtTime(vol, t0 + 0.01);
			g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);

			osc.connect(bp);
			bp.connect(g);
			g.connect(this.master);

			osc.start(t0);
			osc.stop(t0 + dur);

			this._hitUntil = t0 + dur + 0.02;
		}

		popup({ ms = 260, vol = 0.65 } = {}) {
			if (!this.unlocked) return;
			const dur = ms / 1000;
			const t0 = this.ctx.currentTime;

			const oscH = this.ctx.createOscillator();
			const gH = this.ctx.createGain();
			oscH.type = "triangle";
			oscH.frequency.setValueAtTime(1000, t0);
			oscH.frequency.exponentialRampToValueAtTime(1800, t0 + dur);

			const oscL = this.ctx.createOscillator();
			const gL = this.ctx.createGain();
			oscL.type = "sawtooth";
			oscL.frequency.setValueAtTime(230, t0);
			oscL.frequency.exponentialRampToValueAtTime(200, t0 + dur);

			const bp = this.ctx.createBiquadFilter();
			bp.type = "bandpass";
			bp.frequency.setValueAtTime(1400, t0);
			bp.Q.value = 6.5;

			const lp = this.ctx.createBiquadFilter();
			lp.type = "lowpass";
			lp.frequency.setValueAtTime(2200, t0);
			lp.Q.value = 0.8;

			gH.gain.setValueAtTime(0.0001, t0);
			gH.gain.linearRampToValueAtTime(vol, t0 + 0.03);
			gH.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);

			gL.gain.setValueAtTime(vol * 0.4, t0);
			gL.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);

			oscH.connect(gH);
			oscL.connect(gL);
			gH.connect(bp);
			gL.connect(bp);
			bp.connect(lp);
			lp.connect(this.master);

			oscH.start(t0);
			oscL.start(t0);
			oscH.stop(t0 + dur);
			oscL.stop(t0 + dur);
		}

        close({ ms = 120, start = 820, end = 380, vol = 0.55 } = {}) {
            if (!this.unlocked) return;
            const dur = ms / 1000;
            const t0 = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const g = this.ctx.createGain();

            osc.type = "sine";
            osc.frequency.setValueAtTime(start, t0);
            osc.frequency.exponentialRampToValueAtTime(end, t0 + dur);

            g.gain.setValueAtTime(0.0001, t0);
            g.gain.linearRampToValueAtTime(vol, t0 + 0.01);
            g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);

            osc.connect(g);
            g.connect(this.master);
            osc.start(t0);
            osc.stop(t0 + dur);
        }

        success() {
            this.popup({ ms: 100, start: 520, end: 880, vol: 0.5 });
            setTimeout(() => this.popup({ ms: 110, start: 700, end: 1200, vol: 0.45 }), 100);
        }

        error() {
            this.close({ ms: 120, start: 600, end: 400, vol: 0.6 });
            setTimeout(() => this.close({ ms: 140, start: 500, end: 330, vol: 0.55 }), 130);
        }

        setVolume(vol) {
            if (!this.unlocked) return;
            this.master.gain.setValueAtTime(Math.max(0, Math.min(1, vol)), this.ctx.currentTime);
        }
    }

    window.soundFX = new SoundFX();
})();


if (wiseNaviEventOverlay) {
    wiseNaviEventOverlay.addEventListener('pointerup', handleWiseNaviEventClick, false);
}

if(naviCreateButton !== null)
{naviCreateButton.addEventListener('pointerup', async function() {

	let isConfirmed = window.confirm(MSG_REGISTER_CONFIRM[intSelectedLanguage]);
	
	if (isConfirmed) {

		const inputEl = document.querySelector('.naviContentsCreateNewData');
		const inputValue = escapeHTML(inputEl.value);

		const currentUrl = window.location.href;
		const url = new URL(currentUrl);
		const params = url.searchParams;
		const send_unique_code = escapeHTML(params.get(KEY_UNIQUE_CODE));

		let send_array = inputValue
			.split(/\n/)
			.map(item => item.trim())
			.filter(item => item !== '');

		if (send_array.length === LENGTH_EMPTY) {
			alert(MSG_ERROR_INPUT_CONTENT[intSelectedLanguage]);
			return;
		}

		if (send_unique_code == null || send_unique_code === '') {
			alert(MSG_ERROR_UNIQUE_CODE[intSelectedLanguage]);
			return;
		}

		const payload = {
			current_url: currentUrl,
			send_array: send_array,
			unique_code: send_unique_code,
			int_selected_language: intSelectedLanguage
		};

		try {
			await postJson(
				wiseNaviCreateNewContentsUrl,
				payload,
				10000
			);

			location.reload();
			return;

		} catch (error) {
			const message = (error && error.message) ? error.message : '';

			if (message.includes('タイムアウト')) {
				console.error('タイムアウトが発生しました。');
			} else {
				alert(message || 'Error');
			}
			return;
		}
	}

}, false);}


function runWiseScanAndCloseForNavi() {
    if (!wiseScanOverlay) return;
    if (isWiseScanRunning) return;
    isWiseScanRunning = true;

    const targets = Array.from(document.querySelectorAll(
        '#wiseNaviScreen, #wiseMapMessageScreen, #wiseNaviScreen .wiseScanTarget, #wiseMapMessageScreen .wiseScanTarget'
    ));
    currentScanTargets = targets.slice(0);
    let prevLineY = null;

    function isSoftTarget(node) {
        if (!node) return false;
        if (node.id === 'wiseNaviScreen' || node.id === 'wiseMapMessageScreen') return true;
        if (node.matches && node.matches('.wiseScanTargetSoft, [data-wise-glow="soft"]')) return true;
        return false;
    }

    function runWiseScanFrame() {
        if (!wiseScanOverlay.classList.contains('scan-start')) return;
        if (!wiseScanline) return;
        const r = wiseScanline.getBoundingClientRect();
        const lineY = r.top + r.height / 2;
        if (prevLineY === null) {
            prevLineY = lineY;
            requestAnimationFrame(runWiseScanFrame);
            return;
        }
        targets.forEach(el => {
            if (!el || !el.getBoundingClientRect) return;
            const rect = el.getBoundingClientRect();
            const cy = rect.top + rect.height / 1.5;
            if (prevLineY >= cy && lineY < cy) {
                spawnOrPulseScanClone(el, { soft: isSoftTarget(el) });
                // window.soundFX?.hit();
            }
        });
        prevLineY = lineY;
        requestAnimationFrame(runWiseScanFrame);
    }

    requestAnimationFrame(runWiseScanFrame);

    if (wiseScanline) {
        const onScanEnd = () => {
            wiseScanline.removeEventListener('animationend', onScanEnd);
            wiseScanOverlay.classList.add('scan-finish');
            // window.soundFX?.stopScanLoop(220);
            setTimeout(() => {
				gatherScanClonesToToolbar(() => {
					removeAllScanClones(currentScanTargets);
				});
                hideWiseOverlay(wiseScanOverlay);
                isWiseScanRunning = false;
                wise_navi_status = 'scanned';
                showWiseNaviNextLabel('black-text');
            }, HIDE_DELAY_MS);
        };
        wiseScanline.addEventListener('animationend', onScanEnd, { once: true });
    }

    if (typeof wiseMapOverlay !== 'undefined' && wiseMapOverlay !== null) {
        wiseMapOverlay.classList.add('overlay-on');
        wiseMapOverlay.removeAttribute('aria-hidden');
        wiseMapOverlay.inert = false;
    }
}


function stripLanguageTagsFromText(text) {
    if (text == null) return text;
    let result = String(text);
    for (const [openTag, closeTag] of languageTags) {
        result = result.split(openTag).join('');
        result = result.split(closeTag).join('');
    }
    result = result.replace(/\[\/?mute\]/g, '');
    return result;
}

function normalizeTextForSpeech(text) {
    if (!text) return text;
    let result = text;
    result = result.replace(/\[mute\][\s\S]*?\[\/mute\]/g, '');
    for (const rule of speechReplacements) {
        result = result.replace(rule.pattern, rule.replace);
    }
    return result;
}

async function runTypewriterEffect(msgEl, speed = 100, startDelay = 250, done){
    if (!msgEl) { if (done) done(); return; }
    if (msgEl.dataset.typed === '1') { if (done) done(); return; }

    const shouldCompare = (msgEl === wiseMapMessageScreen);

    let original = (msgEl.dataset.originalText || '').trim();
    if (!original) {
        const fallback = (msgEl.textContent || '').trim();
        if (fallback) {
            msgEl.dataset.originalText = fallback;
            original = fallback;
            msgEl.textContent = '';
        }
    }
    if (!original) { if (done) done(); return; }

    const speechText = normalizeTextForSpeech(original);
    const displayText = stripLanguageTagsFromText(original);

    const speechDonePromise = speakTextWithTts(speechText, 1);

    if (msgEl.textContent.trim() !== '') {
        msgEl.textContent = '';
    }
    msgEl.style.display = 'inline-block';

    const span = document.createElement('span');
    span.className = 'wiseTypingText';
    span.setAttribute('aria-live', 'polite');
    span.setAttribute('role', 'status');
    msgEl.appendChild(span);
    msgEl.classList.add('is-typing', 'wiseCyanGlowRing');

    let measureSpan = null;
    let startIndex = INDEX_FIRST;

    if (shouldCompare) {
        measureSpan = document.createElement('span');
        measureSpan.className = span.className;
        measureSpan.style.visibility = 'hidden';
        measureSpan.style.position = 'absolute';
        measureSpan.style.pointerEvents = 'none';
        measureSpan.style.whiteSpace = getComputedStyle(span).whiteSpace;
        measureSpan.style.width = msgEl.clientWidth ? msgEl.clientWidth + 'px' : '';
        msgEl.appendChild(measureSpan);
    }

    let i = INDEX_FIRST;

    function updateVisibleText(){
        if (!shouldCompare) {
            span.textContent = displayText.slice(INDEX_FIRST, i);
            return;
        }
        measureSpan.textContent = displayText.slice(startIndex, i);
        const limitHeight = msgEl.clientHeight || span.clientHeight || LENGTH_EMPTY;
        while (limitHeight > LENGTH_EMPTY && measureSpan.scrollHeight > limitHeight && startIndex < i) {
            startIndex += LAST_INDEX_OFFSET;
            measureSpan.textContent = displayText.slice(startIndex, i);
        }
        span.textContent = measureSpan.textContent;
    }

    function runTypewriterFrame(){
        if (i <= displayText.length){
            i += LAST_INDEX_OFFSET;
            updateVisibleText();
            setTimeout(runTypewriterFrame, speed);
        } else {
            (async () => {
                try { await speechDonePromise; } catch(e) {}
                setTimeout(() => {
					msgEl.classList.remove('is-typing');
					msgEl.dataset.typed = '1';
					if (measureSpan) measureSpan.remove();
					if (done) done();
				}, POST_TYPEWRITER_PAUSE_MS);
            })();
        }
    }

    setTimeout(runTypewriterFrame, startDelay);
}


function resetWiseMessageState(el){
    if (!el) return;
    el.classList.remove('is-typing', 'wiseCyanGlowRing');
    delete el.dataset.typed;
    delete el.dataset.originalText;
    el.replaceChildren();
}


async function renderWiseMapForNavigation({ showOverlay = false } = {}) {
    if (typeof wiseMapOverlay !== 'undefined' && wiseMapOverlay !== null) {
        const uiContainers = document.querySelectorAll('.wiseMapScreenUiContainer');
        uiContainers.forEach(elm => elm.classList.add('hidden'));
        wiseMapButtonsContainer.classList.add('hidden');

        let url = new URL(window.location.href),
            params = url.searchParams,
            uniqueCode = escapeHTML(params.get(KEY_UNIQUE_CODE));

        try {

			const payload = {
				map_type: 'focus_point',
				unique_code: uniqueCode,
				int_selected_language: intSelectedLanguage
			};
			
            const result = await postJson(
                wiseNaviRenderGoalUrl,
                payload,
                60000
            );

			const data = result.data;
			const length = result.length;

            wise_navi_waypoint_length = length ?? 0;
            wise_navi_script_index  = INDEX_FIRST;
            wise_navi_script_length = await fetchWiseNaviScriptsLength(wise_navi_waypoint_index);
            prefetchNextWiseNaviScriptsLength(wise_navi_waypoint_index);

			// applyFontSizeVariation(
			// 	['wiseUiFontSizeTarget'],
			// 	'wiseUiFontSizeTargetVariationDifference'
			// );

        } catch (error) {
            console.error('Error:', error.message || error);
        } finally {
            if (showOverlay) {
                wiseMapOverlay.classList.add('overlay-on');
            } else {
                wiseMapOverlay.classList.remove('overlay-on');
                wiseMapOverlay.setAttribute('aria-hidden', 'true');
                wiseMapOverlay.inert = true;
            }
        }
    }
}

async function fetchWiseNaviScriptsLength(waypointIndex) {
	if (wiseNavigationSequence[waypointIndex] != null) {
		return wiseNavigationSequence[waypointIndex];
	}

	try {
		const url = new URL(window.location.href);
		const params = url.searchParams;
		const uniqueCode = escapeHTML(params.get(KEY_UNIQUE_CODE));

		const payload = {
			waypoint_index: waypointIndex,
			unique_code: uniqueCode,
			int_selected_language: intSelectedLanguage
		};

		const result = await postJson(
			wiseNaviGetScriptsLengthUrl,
			payload,
			20000
		);

		const scripts_length = result.scripts_length;

		wiseNavigationSequence[waypointIndex] = scripts_length ?? 0;
		return wiseNavigationSequence[waypointIndex];

	} catch (e) {
		console.error('fetchWiseNaviScriptsLength error:', e);

		wiseNavigationSequence[waypointIndex] = 0;
		return 0;
	}
}


function prefetchNextWiseNaviScriptsLength(currIndex) {
    const next = currIndex + 1;
    if (next < wise_navi_waypoint_length && wiseNavigationSequence[next] == null) {
        fetchWiseNaviScriptsLength(next).catch(()=>{});
    }
}

async function advanceWiseNaviScript() {
    wise_navi_script_index++;

    if (typeof wise_navi_script_length !== 'number' || isNaN(wise_navi_script_length)) {
        wise_navi_script_length = await fetchWiseNaviScriptsLength(wise_navi_waypoint_index);
    }

    if (wise_navi_script_index >= wise_navi_script_length) {
        wise_navi_script_index = INDEX_FIRST;
        wise_navi_waypoint_index++;

        if (wise_navi_waypoint_index < wise_navi_waypoint_length) {
            wise_navi_script_length = await fetchWiseNaviScriptsLength(wise_navi_waypoint_index);
            prefetchNextWiseNaviScriptsLength(wise_navi_waypoint_index);
        }
    }

    if (wise_navi_waypoint_index >= wise_navi_waypoint_length) {
        wise_navi_status = 'core_finished';
        showWiseNaviNextLabel();
        return;
    }

    wise_navi_status = 'core_rendered';
    showWiseNaviNextLabel();
}

function extractPlainTextFromHtml(input_html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = String(input_html ?? '');
    const text = (tmp.textContent || '').replace(/\s+/g, ' ').trim();
    return text;
}

function ellipsizeText(s, max = 120) {
    if (!s) return '';
    return s.length > max ? s.slice(0, max - 1) + '…' : s;
}

function buildWiseDiffLine(prevText, currentText, addGoal) {
    const prev = normalizeTextContent(prevText);
    const curr = normalizeTextContent(currentText);

    const line = document.createElement('div');
    line.className = 'wise-diff-line';

    if (!addGoal || !prev || prev === curr) {
        const sCurr = document.createElement('span');
        sCurr.className = 'wise-current';
        sCurr.textContent = ellipsizeText(curr);
        line.appendChild(sCurr);
        return line;
    }

    const sPrev = document.createElement('span');
    sPrev.className = 'wise-prev';
    sPrev.textContent = ellipsizeText(prev);

    const sArrow = document.createElement('span');
    sArrow.className = 'wise-arrow';
    sArrow.textContent = ' ⇒ ';

    const sCurr = document.createElement('span');
    sCurr.className = 'wise-current';
    sCurr.textContent = ellipsizeText(curr);

    line.appendChild(sPrev);
    line.appendChild(sArrow);
    line.appendChild(sCurr);
    return line;
}

function normalizeTextContent(s) {
    return (s || '').replace(/\s+/g, ' ').trim();
}

async function renderWiseMapGoalForNavi(addGoal, goal_html) {
    if (!(wiseMapFocusNodeArea && goal_html !== undefined)) return;

    const t = wiseMapFocusNodeArea;
    const ds = t.dataset;

    const extracted = normalizeTextContent(extractPlainTextFromHtml(goal_html || ''));

    ds.prevText = typeof ds.prevText === 'string' ? ds.prevText : '';
    ds.currentText = typeof ds.currentText === 'string' ? ds.currentText : '';

    const isChanged = extracted !== ds.currentText;
    if (isChanged) {
        ds.prevText = ds.currentText;
        ds.currentText = extracted;
    }

    const wrapper = document.createElement('div');
	wrapper.classList.add('wiseUiFontSizeTarget');
    wrapper.appendChild(buildWiseDiffLine(ds.prevText, ds.currentText, addGoal));

    wiseMapFocusMapArea.innerHTML = '';

    const cs = getComputedStyle(t);
    const padTop = parseFloat(cs.paddingTop) || 0;
    const padBottom = parseFloat(cs.paddingBottom) || 0;
    const padLeft = parseFloat(cs.paddingLeft) || 0;
    const padRight = parseFloat(cs.paddingRight) || 0;
    const borTop = parseFloat(cs.borderTopWidth) || 0;
    const borBottom = parseFloat(cs.borderBottomWidth) || 0;

    const contentWidth = Math.max(0, t.clientWidth - padLeft - padRight);

    const meas = document.createElement('div');
    meas.style.position = 'absolute';
    meas.style.visibility = 'hidden';
    meas.style.pointerEvents = 'none';
    meas.style.left = '-99999px';
    meas.style.top = '0';
    meas.style.width = contentWidth + 'px';
    meas.style.boxSizing = 'content-box';
    meas.style.font = cs.font;
    meas.style.lineHeight = cs.lineHeight;
    meas.style.letterSpacing = cs.letterSpacing;
    meas.style.wordBreak = cs.wordBreak;
    meas.style.whiteSpace = cs.whiteSpace;
    meas.style.textTransform = cs.textTransform;

    const contentOnly = wrapper.cloneNode(true);
    meas.appendChild(contentOnly);
    document.body.appendChild(meas);

    if (document.fonts && document.fonts.status !== 'loaded') {
        try { await document.fonts.ready; } catch (_) {}
    }

    const contentH = contentOnly.getBoundingClientRect().height;
    const targetH = Math.ceil(contentH + padTop + padBottom + borTop + borBottom);

    meas.remove();

    const prevOv = t.style.overflow;

    t.classList.add('newRendered');
    t.style.height = targetH + 'px';
    t.style.overflow = 'hidden';

    await animateMount(wrapper, {
        mode: 'replace',
        targetEl: t,
        duration: 900,
        easing: 'cubic-bezier(.22,.61,.36,1)',
        lockHeightDuring: false,
        keepLocked: true
    });

    t.style.height = targetH + 'px';
    t.style.overflow = prevOv || 'visible';
}

async function renderWiseMapContentForNavi(speed = 100, startDelay = 250){

	wise_navi_status = 'rendering';
	hideWiseNaviNextLabel();

	resetWiseMessageState(wiseMapMessageScreen);

	if (typeof wiseMapOverlay !== 'undefined' && wiseMapOverlay !== null) {
		
		try {

			const url = new URL(window.location.href);
			const params = url.searchParams;
			const uniqueCode = escapeHTML(params.get(KEY_UNIQUE_CODE));

			const payload = {
				unique_code: uniqueCode,
				wise_navi_waypoint_index: wise_navi_waypoint_index,
				wise_navi_script_index: wise_navi_script_index,
				int_selected_language: intSelectedLanguage
			};

			const result = await postJson(
				wiseNaviRenderContentUrl,
				payload,
				60000
			);
			const data = result.data;

			if (
				data != null &&
				(!Array.isArray(data) || data.length > 0) &&
				(typeof data !== 'object' || Array.isArray(data) || Object.keys(data).length > 0)
			) {

				const script_type_id = escapeNumber(data['script_type_id']);
				const goal_html = data['item_title'];
				switch(script_type_id){
						
					case SCRIPT_TYPE_LAYER_GOAL:
						clearWiseMapNewRenderedState();
						await renderWiseMapGoalForNavi(true, goal_html);
						await new Promise(resolve => setTimeout(resolve, WAIT_DELAY_MS));
						await advanceWiseNaviScript();
						break;
					case SCRIPT_TYPE_RESET_GOAL:
						clearWiseMapNewRenderedState();
						await renderWiseMapGoalForNavi(false, goal_html);
						await new Promise(resolve => setTimeout(resolve, WAIT_DELAY_MS));
						await advanceWiseNaviScript();
						break;
					case SCRIPT_TYPE_LAYER_CONTENT:
						clearWiseMapNewRenderedState();
						const items = toArrayMaybe(data);
						const container = renderWiseMapContentContainer(null, items, true, true);
						await animateMount(container, {
							mode: 'prepend',
							parentEl: wiseMapFocusMapArea,
							duration: 900,
							easing: 'cubic-bezier(.22,.61,.36,1)',
							effect: 'gap',
							gapRatio: 0.6
						});

						// applyFontSizeVariation(
						// 	['wiseUiFontSizeTarget'],
						// 	'wiseUiFontSizeTargetVariationDifference'
						// );

						await new Promise(resolve => setTimeout(resolve, WAIT_DELAY_MS));
						await advanceWiseNaviScript();
						break;
						
					case SCRIPT_TYPE_MESSAGE_FREE:
					case SCRIPT_TYPE_MESSAGE_START_POINT:
					case SCRIPT_TYPE_MESSAGE_NEXT_POINT:
					case SCRIPT_TYPE_MESSAGE_COUNT_PARTICLE:
					case SCRIPT_TYPE_MESSAGE_EXPLAIN_PARTICLE:
					case SCRIPT_TYPE_MESSAGE_COUNT_GRAMMAR:
					case SCRIPT_TYPE_MESSAGE_EXPLAIN_GRAMMAR:
					case SCRIPT_TYPE_MESSAGE_COUNT_INFLECTION:
					case SCRIPT_TYPE_MESSAGE_EXPLAIN_INFLECTION:
					case SCRIPT_TYPE_MESSAGE_COUNT_SENTENCE:
					case SCRIPT_TYPE_MESSAGE_EXPLAIN_SENTENCE:
					case SCRIPT_TYPE_MESSAGE_COMBINE_READY:
					case SCRIPT_TYPE_MESSAGE_COMBINE_RESULT:
					case SCRIPT_TYPE_MESSAGE_INTRO_OVERVIEW:
					case SCRIPT_TYPE_MESSAGE_INTRO_GO:
					case SCRIPT_TYPE_MESSAGE_NO_ANALYSIS:
					default:
						wise_navi_status = 'typing';
						wiseMapMessageScreen.dataset.originalText = escapeHTML(data['message']);
						await runTypewriterEffect(wiseMapMessageScreen, speed, startDelay, async function(){
							await advanceWiseNaviScript();
						});
						break;
				}
			}

		} catch (error) {
			console.error('Error:', error.message || error);
			wise_navi_status = 'rendered';
			showWiseNaviNextLabel();
		}
	}
}

function clearWiseMapNewRenderedState() {
	document.querySelectorAll('.newRendered').forEach(el => {
		el.classList.remove('newRendered');
	});
}

function collectWiseOpeningMessages(){
    const list = Array.from(document.querySelectorAll('.wiseNaviMessageOpening'));
    return list.sort((a, b) => (Number(a.dataset.sort) || 0) - (Number(b.dataset.sort) || 0));
}

function collectWiseEndingMessages(){
    const list = Array.from(document.querySelectorAll('.wiseNaviMessageEnding'));
    return list.sort((a, b) => (Number(a.dataset.sort) || 0) - (Number(b.dataset.sort) || 0));
}

function showOnlyOneInGroup(group, index){
    for (let i = 0; i < group.length; i++) {
        const el = group[i];
        if (!el) continue;
        el.style.display = (i === index) ? 'inline-block' : 'none';
    }
}


function startWiseOpeningAt(index, speed = 100, startDelay = 200){
    const openingMsgs = collectWiseOpeningMessages();
    if (!openingMsgs.length) {
        wise_navi_status = 'opening_finished';
        showWiseNaviNextLabel();
        return;
    }
    openingIndex = Math.max(0, Math.min(index, openingMsgs.length - 1));
    if (wiseOpeningOverlay) {
        wiseOpeningOverlay.classList.add('overlay-on');
    }
    hideWiseNaviNextLabel();
    showOnlyOneInGroup(openingMsgs, openingIndex);
    const msg = openingMsgs[openingIndex];
    if (!msg) {
        wise_navi_status = 'opening_finished';
        showWiseNaviNextLabel();
        return;
    }
    const isLast = openingIndex >= openingMsgs.length - 1;
    if (msg.dataset.typed !== '1') {
        wise_navi_status = 'typing';
        runTypewriterEffect(msg, Math.max(60, speed - 20), startDelay, function(){
            msg.dataset.typed = '1';
            wise_navi_status = isLast ? 'opening_finished' : 'opening_progress';
            showWiseNaviNextLabel();
        });
    } else {
        wise_navi_status = isLast ? 'opening_finished' : 'opening_progress';
        showWiseNaviNextLabel();
    }
}

function proceedWiseOpening(speed = 100, startDelay = 200){
    const openingMsgs = collectWiseOpeningMessages();
    if (!openingMsgs.length) {
        wise_navi_status = 'opening_finished';
        return;
    }
	if (wise_navi_status === 'wait') {
		const topicMsg = document.getElementById('wiseStartOverlayTopicMessage');
		if (topicMsg) {
			topicMsg.style.display = 'none';
			topicMsg.classList.remove('is-typing', 'wiseCyanGlowRing');
		}
		startWiseOpeningAt(0, speed, startDelay);
	}
    if (wise_navi_status === 'typing') return;
    if (wise_navi_status === 'opening_finished') return;
    if (openingIndex < 0) openingIndex = 0;
    const nextIdx = openingIndex + 1;
    if (nextIdx >= openingMsgs.length) {
        wise_navi_status = 'opening_finished';
        showWiseNaviNextLabel();
    } else {
        startWiseOpeningAt(nextIdx, speed, startDelay);
    }
}

async function startWisePreEnding(options) {

	const speed = (options && typeof options.speed === 'number') ? options.speed : 96;
    const startDelay = (options && typeof options.startDelay === 'number') ? options.startDelay : 200;
	
    wise_navi_status = 'pre_ending';
    hideWiseNaviNextLabel();
    resetWiseMessageState(wiseMapMessageScreen);

	const message_type = 'pre_ending';
	
	try {

		const payload = {
			int_selected_language: intSelectedLanguage,
			message_type: message_type
		};

		const result = await postJson(
			wiseNaviGetMessagesUrl,
			payload,
			60000
		);
		const data = result.data;

		if (
			data != null &&
			(!Array.isArray(data) || data.length > 0) &&
			(typeof data !== 'object' || Array.isArray(data) || Object.keys(data).length > 0)
		) {
			if (data.messages && Array.isArray(data.messages)) {

				const msg = data.messages;

				if (wiseMapMessageScreen) {
					wiseMapMessageScreen.dataset.originalText = msg;
					runTypewriterEffect(wiseMapMessageScreen, speed, startDelay, function () {
						wise_navi_status = 'pre_ending_finished';
						showWiseNaviNextLabel();
					});
				} else {
					wise_navi_status = 'pre_ending_finished';
					showWiseNaviNextLabel();
				}
			}
		}

	} catch (error) {
		console.error('Error:', error.message || error);
	}   
}

async function startWiseSummary(options){
    const speed = (options && typeof options.speed === 'number') ? options.speed : 96;
    const startDelay = (options && typeof options.startDelay === 'number') ? options.startDelay : 200;

    wise_navi_status = 'summary';
    hideWiseNaviNextLabel();
    if (wiseMapFocusMapArea) wiseMapFocusMapArea.replaceChildren();
    resetWiseMessageState(wiseMapMessageScreen);

    try {

		const urlObj = new URL(window.location.href);
		const params = urlObj.searchParams;
		const uniqueCode = escapeHTML(params.get(KEY_UNIQUE_CODE)) || 0;

		const payload = {
			unique_code: uniqueCode,
			int_selected_language: intSelectedLanguage
		};

        const result = await postJson(
			wiseNaviGetSummaryItemsUrl,
			payload,
			20000
		);
		const data = result.data;

		const items = toArrayMaybe(data.items);

		if (items.length === LENGTH_EMPTY) {
		} else {
			for (const item of items) {
				const container = renderWiseMapContentContainer(null, [item], false, true);
				await animateMount(container, {
					mode: 'prepend',
					parentEl: wiseMapFocusMapArea,
					duration: 900,
					easing: 'cubic-bezier(.22,.61,.36,1)',
					effect: 'gap',
					gapRatio: 0.6
				});

				// applyFontSizeVariation(
				// 	['wiseUiFontSizeTarget'],
				// 	'wiseUiFontSizeTargetVariationDifference'
				// );
				await new Promise(resolve => setTimeout(resolve, WAIT_DELAY_MS));
			}
		}


    } catch (e) {
        console.log('error:');
    }

	const message_type = 'summary';
	
	try {

		const payload = {
			int_selected_language: intSelectedLanguage,
			message_type: message_type
		};

		const result = await postJson(
			wiseNaviGetMessagesUrl,
			payload,
			60000
		);
		const data = result.data;

		if (
			data != null &&
			(!Array.isArray(data) || data.length > 0) &&
			(typeof data !== 'object' || Array.isArray(data) || Object.keys(data).length > 0)
		) {
			if (data.messages && Array.isArray(data.messages)) {
				
				const msg = data.messages;
				if (wiseMapMessageScreen) {
					wiseMapMessageScreen.dataset.originalText = msg;
					runTypewriterEffect(wiseMapMessageScreen, speed, startDelay, function () {
						wise_navi_status = 'summary_finished';
						showWiseNaviNextLabel('black-text');
					});
				} else {
					wise_navi_status = 'summary_finished';
					showWiseNaviNextLabel('black-text');
				}
			}
		}

	} catch (error) {
		console.error('Error:', error.message || error);
	}
}


function startWiseEndingAt(index, speed = 100, startDelay = 160){
    const endingMsgs = collectWiseEndingMessages();
    if (!endingMsgs.length) {
        finishWiseEnding();
        return;
    }
    endingIndex = Math.max(0, Math.min(index, endingMsgs.length - 1));
    if (wiseOpeningOverlay) {
        const openings = collectWiseOpeningMessages();
        showOnlyOneInGroup(openings, -1);
        wiseOpeningOverlay.classList.add('overlay-on');
    }
    hideWiseNaviNextLabel();
    showOnlyOneInGroup(endingMsgs, endingIndex);
    const msg = endingMsgs[endingIndex];
    if (!msg) {
        finishWiseEnding();
        return;
    }
    const isLast = endingIndex >= endingMsgs.length - 1;
    if (msg.dataset.typed !== '1') {
        wise_navi_status = 'typing';
        runTypewriterEffect(msg, Math.max(60, speed - 20), startDelay, function(){
            msg.dataset.typed = '1';
            wise_navi_status = isLast ? 'ending_ready' : 'ending_progress';
            showWiseNaviNextLabel();
        });
    } else {
        wise_navi_status = isLast ? 'ending_ready' : 'ending_progress';
        showWiseNaviNextLabel();
    }
}

function proceedWiseEnding(speed = 100, startDelay = 160){
    const endingMsgs = collectWiseEndingMessages();
    if (!endingMsgs.length) {
        finishWiseEnding();
        return;
    }
    if (wise_navi_status === 'ending_ready') return;
    if (wise_navi_status === 'core_finished') {
        startWiseEndingAt(0, speed, startDelay);
        return;
    }
    if (wise_navi_status === 'typing') return;
    if (endingIndex < 0) endingIndex = 0;
    const nextIdx = endingIndex + 1;
    if (nextIdx >= endingMsgs.length) {
        wise_navi_status = 'ending_ready';
        showWiseNaviNextLabel();
    } else {
        startWiseEndingAt(nextIdx, speed, startDelay);
    }
}

function finishWiseEnding(){
    hideWiseNaviNextLabel();
    const endings = collectWiseEndingMessages();
    showOnlyOneInGroup(endings, -1);
    wise_navi_status = 'done';
}


async function handleWiseNaviEventClick(e){
    e.preventDefault();
    e.stopPropagation();
    switch (wise_navi_status) {
        case 'wait':
			// デバッグ用ショートカット
			// startWiseWaitToScanTransition(wiseOpeningOverlay);
			// wise_navi_waypoint_index = 3;
			// wise_navi_script_index = INDEX_FIRST;
			// break;
			// デバッグ用ショートカット
			if (!autoClickInterval) {
                autoClickInterval = setInterval(() => {
                    if (typeof wiseNaviEventOverlay !== "undefined" && wiseNaviEventOverlay) {
                        handleWiseNaviEventClick(e);
                    }
                }, 200);
            }
            proceedWiseOpening();
            break;
        case 'typing':
            break;
        case 'opening_progress':
            proceedWiseOpening();
            break;
        case 'opening_finished':
            if (typeof startWiseWaitToScanTransition === 'function') startWiseWaitToScanTransition(wiseOpeningOverlay);
            break;
        case 'scanning':
            break;
        case 'scanned':
            if (typeof renderWiseMapContentForNavi === 'function') renderWiseMapContentForNavi();
            break;
        case 'core_rendering':
            break;
        case 'core_rendered':
            if (typeof renderWiseMapContentForNavi === 'function') renderWiseMapContentForNavi();
            break;
        case 'core_finished':
            if (typeof startWisePreEnding === 'function') startWisePreEnding();
            break;
        case 'pre_ending':
            break;
        case 'pre_ending_finished':
            if (typeof startWiseSummary === 'function') startWiseSummary();
            break;
        case 'summary':
            break;
        case 'summary_finished':
            if (typeof startWiseEndingAt === 'function') startWiseEndingAt(0, 100, 160);
            break;
        case 'ending_progress':
            proceedWiseEnding();
            break;
        case 'ending_ready':
			if (autoClickInterval) {
                clearInterval(autoClickInterval);
                autoClickInterval = null;
            }
            finishWiseEnding();
            break;
        default:
            break;
    }
}

function waitForSpeechVoicesReady(timeoutMs = 2000) {
    return new Promise((resolve) => {
        const ready = () => {
            const v = speechSynthesis.getVoices();
            if (v && v.length) resolve(v);
            else resolve([]);
        };
        const vs = speechSynthesis.getVoices();
        if (vs && vs.length) return resolve(vs);
        const onChange = () => { speechSynthesis.removeEventListener('voiceschanged', onChange); ready(); };
        speechSynthesis.addEventListener('voiceschanged', onChange);
        setTimeout(() => {
            speechSynthesis.removeEventListener('voiceschanged', onChange);
            ready();
        }, timeoutMs);
    });
}

function selectSpeechVoiceByLanguage(lang) {
    const voices = speechSynthesis.getVoices() || [];
    const sameLang = voices.filter(v => (v.lang || '').toLowerCase().startsWith(lang.toLowerCase()));
    const preferred = sameLang.find(v => /Natural|Neural|Online/i.test(v.name)) 
                   || sameLang.find(v => /Microsoft|Google|Apple/i.test(v.name))
                   || sameLang[0];
    return preferred || voices[0] || null;
}


function detectLanguageFromText(text, fallbackLang) {
    const hasHira = /[\u3040-\u309F]/.test(text);
    const hasKata = /[\u30A0-\u30FF\u31F0-\u31FF]/.test(text);
    const hasLatin = /[A-Za-z]/.test(text);
    const hasCJK = /[\u4E00-\u9FFF]/.test(text);
    if (hasHira || hasKata) return "ja-JP";
    if (hasLatin && !hasCJK) return "en-US";
    if (hasCJK && /[我你妳他她們吗嗎的了，、。]/.test(text)) return "zh-TW";
    if (hasCJK && !hasHira && !hasKata) return fallbackLang;
    return fallbackLang;
}

function parseLanguageTaggedSegments(line, defaultLang) {
    const opens = languageTags.map(t => t[0]);
    const closes = languageTags.map(t => t[1]);

    const findOpen = (s, from) => {
        let pos = -1, which = -1;
        for (let i = 0; i < opens.length; i++) {
            const p = s.indexOf(opens[i], from);
            if (p !== -1 && (pos === -1 || p < pos)) {
                pos = p; which = i;
            }
        }
        return { pos, which };
    };

    const segments = [];
    let i = 0;
    const L = line.length;

    while (i < L) {
        const { pos, which } = findOpen(line, i);
        if (pos === -1) {
            const rest = line.slice(i).trim();
            if (rest) segments.push({ text: rest, lang: null });
            break;
        }
        const pre = line.slice(i, pos).trim();
        if (pre) segments.push({ text: pre, lang: null });

        const openTag = opens[which];
        const closeTag = closes[which];
        const afterOpen = pos + openTag.length;
        const closePos = line.indexOf(closeTag, afterOpen);

        if (closePos === -1) {
            const rest = line.slice(pos).replace(openTag, "").trim();
            if (rest) segments.push({ text: rest, lang: null });
            break;
        }

        const inner = line.slice(afterOpen, closePos).trim();
        if (inner) {
            const tagKey = openTag.slice(1, -1);
            const explicitLang = tagLangMap[tagKey] || defaultLang;
            segments.push({ text: inner, lang: explicitLang });
        }

        i = closePos + closeTag.length;
    }

    return segments;
}


async function speakTextWithTts(original, rate = 1.0, pitch = 1.0, gapMs = 150) {
    await waitForSpeechVoicesReady();
    if (__speakBusy) speechSynthesis.cancel();
    __speakBusy = true;

    const idx = Number.isInteger(intSelectedLanguage) && intSelectedLanguage >= 0 && intSelectedLanguage < languages.length
        ? intSelectedLanguage
        : 0;
    const fallbackLang = languages[idx] || "ja-JP";

    const lines = String(original || "")
        .split(/\r?\n+/)
        .map(s => s.trim())
        .filter(Boolean);

    for (const line of lines) {
        const segs = parseLanguageTaggedSegments(line, fallbackLang);

        for (const seg of segs) {
            const txt = seg.text;
            if (!txt) continue;

            const lang = seg.lang || detectLanguageFromText(txt, fallbackLang);

            const u = new SpeechSynthesisUtterance(txt);
            u.lang = lang;

            const voice = selectSpeechVoiceByLanguage(lang);
            if (voice) u.voice = voice;

            u.rate = rate;
            u.pitch = pitch;

            await new Promise(res => {
                u.onend = res;
                u.onerror = res;
                speechSynthesis.speak(u);
            });

            if (gapMs) {
                await new Promise(r => setTimeout(r, gapMs));
            }
        }
    }

    __speakBusy = false;
}


async function reorderWiseNaviContents(isPrevious, elm) {
    const isPreviousAsNumber = isPrevious ? FLAG_TRUE : FLAG_FALSE;
    const id = escapeNumber(elm.dataset.id);
    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const params = url.searchParams;
    const unique_code = params.get(KEY_UNIQUE_CODE) || 0;

    const payload = {
        isPreviousAsNumber: isPreviousAsNumber,
        currentUrl: currentUrl,
        id: id,
        unique_code: unique_code,
        int_selected_language: intSelectedLanguage
    };

    await executeRoomLessonContentResort(wiseNaviResortContentsUrl, payload, {
        onSuccess: () => { location.reload(); }
    });
}

async function searchWiseNaviItems() {
    try {
        const arr_navi_items = await fetchWiseNaviItems();
        renderWiseNaviItems(arr_navi_items);
    } catch (error) {
        console.error('Failed to search navi items:', error.message || error);
    }
}

async function fetchWiseNaviItems() {
    const urlObj = new URL(window.location.href);
    const params = urlObj.searchParams;
    const unique_code = params.get(KEY_UNIQUE_CODE) || 0;

    const payload = {
        unique_code: unique_code,
        int_selected_language: intSelectedLanguage
    };

    const result = await postJson(wiseNaviGetItemsUrl, payload, 10000);
	const data = result.data;

    if (Array.isArray(data) && data.length === LENGTH_EMPTY) {
        return [];
    }

    return data;
}

function renderWiseNaviItems(arr_navi_items){

	let elm_targetUl = document.getElementById('naviItemsSideMenuSelectedContentsUl'),
	classNaming_li = 'naviItemsSideMenuSelectedContentsLi';

	elm_targetUl.replaceChildren();

	for(let i = INDEX_FIRST; i < arr_navi_items.length; i++){
		let targetArray = arr_navi_items[i];
		let int_item_id = escapeNumber(targetArray['itemId']),
		int_layer_id = escapeNumber(targetArray['layerId']),
		str_layer_name = escapeHTML(targetArray['layerName']),
		int_sort = escapeHTML(targetArray['sort']);

		let elm_li = document.createElement('li');
		elm_li.classList.add(classNaming_li);
		elm_li.dataset.id = int_item_id;
		elm_li.dataset.layerId = int_layer_id;
		elm_li.dataset.layerName = str_layer_name;
		elm_li.dataset.sort = int_sort;

		let elm_deleteButton = document.createElement('button');
        elm_deleteButton.classList.add('naviContentsDeleteButton');
		elm_deleteButton.dataset.id = int_item_id;
		elm_deleteButton.textContent = 'del';

		let elm_previousButton = document.createElement('button');
		elm_previousButton.classList.add('naviContentsEditContentsSortPreviousButton', 'naviItemsEditContentsSortButton');
		elm_previousButton.dataset.id = int_item_id;
		elm_previousButton.textContent = '△';

		let elm_nextButton = document.createElement('button');
		elm_nextButton.classList.add('naviContentsEditContentsSortNextButton', 'naviItemsEditContentsSortButton');
		elm_nextButton.dataset.id = int_item_id;
		elm_nextButton.textContent = '▽';

		let elm_namesSpan = document.createElement('span');
		elm_namesSpan.classList.add('naviItemsNamesSpan');
		elm_namesSpan.textContent = str_layer_name;

		elm_li.appendChild(elm_deleteButton);
		elm_li.appendChild(elm_previousButton);
		elm_li.appendChild(elm_nextButton);
		elm_li.appendChild(elm_namesSpan);

		elm_targetUl.appendChild(elm_li);
	}
}


function showWiseNaviNextLabel(colorClass) {
	if(wiseNaviNextLabel){
		wiseNaviNextLabel.classList.remove('hidden');
        if (colorClass === 'white-text' || colorClass === 'black-text') {
			wiseNaviNextLabel.classList.remove('white-text', 'black-text');
            wiseNaviNextLabel.classList.add(colorClass);
        }
	}
}

function hideWiseNaviNextLabel() {
	if(wiseNaviNextLabel){
		wiseNaviNextLabel.classList.add('hidden');
	}
}









document.addEventListener('pointerup', async (e) => {

    const li = e.target.closest('.naviItemsSideMenuAddContentsLi');
    if (!li) {
        return;
    }

    const send_layer_id = escapeNumber(li.dataset.layerId);

    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const params = url.searchParams;
    const send_unique_code = escapeHTML(params.get(KEY_UNIQUE_CODE));

    if (send_unique_code == null || send_unique_code === '') {
        alert(MSG_ERROR_UNIQUE_CODE[intSelectedLanguage]);
        return;
    }

    if (send_layer_id == null || send_layer_id === 0) {
        alert(MSG_ERROR_INPUT_CONTENT[intSelectedLanguage]);
        return;
    }

    const payload = {
        current_url: currentUrl,
        send_layer_id: send_layer_id,
        unique_code: send_unique_code,
        int_selected_language: intSelectedLanguage
    };

    try {

        await postJson(
            wiseNaviCreateNewContentsUrl,
            payload,
            10000
        );

        if (typeof searchWiseNaviItems === 'function') {
            searchWiseNaviItems();
        }

        return;

    } catch (error) {

        const message = (error && typeof error.message === 'string') ? error.message : '';

        if (message.includes('タイムアウト')) {
            console.error('タイムアウトが発生しました。');
        } else {
            alert(message || 'Error');
        }

        return;
    }
});

document.addEventListener('pointerup', (event) => {

    const btn = event.target.closest('.naviContentsDeleteButton');
    if (!btn) {
        return;
    }

    event.stopPropagation();

    const isConfirmed = window.confirm(MSG_DELETE_CONFIRM[intSelectedLanguage]);
    if (!isConfirmed) {
        return;
    }

    const id = escapeNumber(btn.dataset.id);

    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const params = url.searchParams;
    const unique_code = params.get(KEY_UNIQUE_CODE) || 0;

    const payload = {
        currentUrl: currentUrl,
        id: id,
        unique_code: unique_code,
        int_selected_language: intSelectedLanguage
    };

    executeManagePageDeletion(wiseNaviDeleteContentsUrl, payload, {
        onSuccess: () => { location.reload(); }
    });
});

document.addEventListener('pointerup', (event) => {

    const btn = event.target.closest(
        '.naviContentsEditContentsSortPreviousButton, .naviContentsEditContentsSortNextButton'
    );
    if (!btn) {
        return;
    }

    event.stopPropagation();

    const isPrevious = btn.classList.contains('naviContentsEditContentsSortPreviousButton');

    reorderWiseNaviContents(isPrevious, btn);
});


function initWiseNaviItemsLayout(isOnload) {

    if (naviItemsBody === null) return;

    if (isOnload) {
        searchWiseNaviItems();
    }

    finalizeLayout();
}

function bindWiseNaviItemsLayoutEvents() {

    if (naviItemsBody === null) return;

    const onResize = throttle(() => {
        prepareLayoutOnResize();
        initWiseNaviItemsLayout(false);
    }, 150);

    window.addEventListener('resize', onResize, { passive: true });
    // window.addEventListener('orientationchange', onResize, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {

    if (naviItemsBody === null) return;

    prepareLayoutOnLoad();
    initWiseNaviItemsLayout(true);
    bindWiseNaviItemsLayoutEvents();

}, { passive: true });


document.addEventListener("DOMContentLoaded", function () {
    initManageWiseNavigationScriptsPage();
});

function initManageWiseNavigationScriptsPage() {
    if (!location.pathname.includes("manage-wise-navigation-scripts")) return;

    const target = document.querySelector("select.naviContentsCreateNewData");
    if (!target) return;

    target.focus();
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });
    });
}


clickHandlers['navi:navigate'] = function (btn) {

    const pagePath = btn.dataset.pagePath;
    const uniqueCode = btn.dataset.uniqueCode;

    if (!pagePath) {
        console.warn('pagePathが指定されていません');
        return;
    }
    if (!uniqueCode) {
        console.warn('uniqueCodeが指定されていません');
        return;
    }

    let baseUrl = currentHomeUrl + pagePath;
    let urlWithParams = `${baseUrl}/?${KEY_UNIQUE_CODE}=${encodeURIComponent(uniqueCode)}`;

    window.open(urlWithParams, '_blank', 'noopener');

};
