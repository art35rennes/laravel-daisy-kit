export function createTransport(notify) {
    const requests = new Map();
    let active = true;

    function cancel(key) {
        const request = requests.get(key);
        if (!request) return;
        requests.delete(key);
        request.abort();
        notify(false, key);
    }

    async function request(key, url) {
        cancel(key);
        const controller = new AbortController();
        requests.set(key, controller);
        notify(true, key);
        try {
            const response = await fetch(url, { credentials: 'same-origin', signal: controller.signal });
            if (!response.ok) throw new Error('Invalid tree response.');
            const data = await response.json();
            if (!active || controller.signal.aborted || requests.get(key) !== controller) return null;
            if (!data || !Array.isArray(data.items)
                || (data.nextCursor !== undefined && data.nextCursor !== null
                    && (!['string', 'number'].includes(typeof data.nextCursor)
                        || String(data.nextCursor).length === 0))) {
                throw new Error('Invalid tree response.');
            }
            return {
                items: data.items,
                nextCursor: data.nextCursor === undefined || data.nextCursor === null ? null : String(data.nextCursor),
            };
        } catch (error) {
            if (!active || controller.signal.aborted || requests.get(key) !== controller) return null;
            throw error;
        } finally {
            if (requests.get(key) === controller) {
                requests.delete(key);
                if (active) notify(false, key);
            }
        }
    }

    function destroy() {
        active = false;
        requests.forEach((controller) => controller.abort());
        requests.clear();
    }

    return { request, cancel, destroy };
}
