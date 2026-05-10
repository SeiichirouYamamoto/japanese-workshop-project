
function fetchWithTimeout(url, options = {}, timeoutMs = 60000) {
    let controller;
    let id;

    if (timeoutMs && timeoutMs > 0) {
        controller = new AbortController();
        id = setTimeout(() => {
            controller.abort();
        }, timeoutMs);
        options.signal = controller.signal;
    }

    return fetch(url, options)
        .finally(() => {
            if (id) clearTimeout(id);
        })
        .catch(err => {
            if (err.name === 'AbortError') {
                throw new Error('リクエストがタイムアウトしました');
            }
            throw err;
        });
}

async function postJson(url, payload, timeoutMs = 10000) {

    const res = await fetchWithTimeout(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json; charset=utf-8' },
        body: JSON.stringify(payload)
    }, timeoutMs);

    // ★先に本文を読む（HTTP 401でもmessageを拾うため）
    let text = '';
    try {
        text = await res.text();
    } catch (e) {
        text = '';
    }

    let json = null;
    if (text) {
        try {
            json = JSON.parse(text);
        } catch (e) {
            json = null;
        }
    }

    // ★HTTPエラー：respond_error の message を優先
    if (!res.ok) {
        const msg = json?.message || `HTTPエラー: ${res.status}`;
        const err = new Error(msg);
        err.httpStatus = res.status;
        err.response = json;
        throw err;
    }

    // ★ここからはHTTP成功(200系)。JSONがないのは異常
    if (!json) {
        throw new Error('JSONの解析に失敗しました（サーバーがJSON以外を返しています）');
    }

    // ★APIエラー（200で status:error を返す運用も拾える）
    if (json.status !== 'success') {
        throw new Error(json.message || 'APIエラー');
    }

    return json;
}
