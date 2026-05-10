(function () {

    const ROOT_SELECTOR = '.membershipGrid';
    const KEY_ATTR = 'data-eq-key';
    const TOLERANCE_PX = 2;

    function getRoot() {
        return document.querySelector(ROOT_SELECTOR);
    }

    function resetHeights(nodes) {
        for (let i = 0; i < nodes.length; i++) {
            nodes[i].style.height = '';
        }
    }

    function groupByRow(nodes, tolerancePx) {
        const rows = [];

        for (let i = 0; i < nodes.length; i++) {
            const el = nodes[i];
            const top = Math.round(el.getBoundingClientRect().top);

            let row = null;
            for (let r = 0; r < rows.length; r++) {
                if (Math.abs(rows[r].top - top) <= tolerancePx) {
                    row = rows[r];
                    break;
                }
            }

            if (!row) {
                row = { top: top, items: [] };
                rows.push(row);
            }

            row.items.push(el);
        }

        return rows;
    }

    function applyEqualHeightByRow(nodes) {
        if (!nodes || nodes.length === 0) {
            return;
        }

        resetHeights(nodes);

        const rows = groupByRow(nodes, TOLERANCE_PX);

        for (let r = 0; r < rows.length; r++) {
            const items = rows[r].items;

            let maxH = 0;
            for (let i = 0; i < items.length; i++) {
                const h = items[i].getBoundingClientRect().height;
                if (h > maxH) {
                    maxH = h;
                }
            }

            for (let i = 0; i < items.length; i++) {
                items[i].style.height = maxH + 'px';
            }
        }
    }

    function collectTargetsByKey(root) {
        const nodes = Array.from(root.querySelectorAll('[' + KEY_ATTR + ']'));
        const map = new Map();

        for (let i = 0; i < nodes.length; i++) {
            const el = nodes[i];
            const key = el.getAttribute(KEY_ATTR) || '';

            if (!map.has(key)) {
                map.set(key, []);
            }
            map.get(key).push(el);
        }

        return map;
    }

	function updateMembershipFeeVisibility() {
		const select = document.querySelector('select[name="apply_level"]');
		if (!select) {
			return;
		}

		const level = select.value;

		const items = document.querySelectorAll('.membershipApplyFeeItem');
		for (let i = 0; i < items.length; i++) {
			items[i].classList.add('hidden');
		}

		const target = document.querySelector('.membershipApplyFeeItem[data-membership-level="' + level + '"]');
		if (target) {
			target.classList.remove('hidden');
		}
	}

	function initMembershipApplyFeeToggle() {
		const select = document.querySelector('select[name="apply_level"]');
		if (!select) {
			return;
		}

		updateMembershipFeeVisibility();
		select.addEventListener('change', updateMembershipFeeVisibility);
	}



    function run() {
        const root = getRoot();
        if (!root) {
            return;
        }

        requestAnimationFrame(function () {
            const map = collectTargetsByKey(root);
            map.forEach(function (nodes) {
                applyEqualHeightByRow(nodes);
            });
        });
    }

    function initVipCloseTabButton() {
        document.addEventListener('pointerup', function (e) {
            const btn = e.target.closest('[data-action="vip-close-tab"]');
            if (!btn) {
                return;
            }

            e.preventDefault();

            window.close();
        });
    }

    function initMembershipUi() {
		initVipCloseTabButton();
		initMembershipApplyFeeToggle();
	}


    function membershipDebounce(fn, delayMs) {
        let timerId = null;

        return function () {
            if (timerId !== null) {
                clearTimeout(timerId);
            }
            timerId = setTimeout(fn, delayMs);
        };
    }

    const runDebounced = membershipDebounce(run, 60);

    window.addEventListener('load', function () {
        initMembershipUi();
        runDebounced();
    });
    window.addEventListener('resize', runDebounced);
    // window.addEventListener('orientationchange', runDebounced);

    if (window.ResizeObserver) {
        const root = getRoot();
        if (root) {
            const ro = new ResizeObserver(runDebounced);
            ro.observe(root);
        }
    }

})();
