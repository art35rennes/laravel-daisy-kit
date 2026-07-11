function cloneSnapshot(snapshot) {
    return JSON.parse(JSON.stringify(snapshot));
}

export function createHistory(initial, limit = 50) {
    const maximum = Math.max(2, Number(limit) || 50);
    let snapshots = [cloneSnapshot(initial)];
    let index = 0;

    return {
        current() {
            return cloneSnapshot(snapshots[index]);
        },
        record(snapshot) {
            snapshots = snapshots.slice(0, index + 1);
            snapshots.push(cloneSnapshot(snapshot));

            if (snapshots.length > maximum) {
                snapshots.shift();
            }

            index = snapshots.length - 1;

            return this.current();
        },
        replace(snapshot) {
            snapshots[index] = cloneSnapshot(snapshot);

            return this.current();
        },
        canUndo() {
            return index > 0;
        },
        canRedo() {
            return index < snapshots.length - 1;
        },
        undo() {
            if (this.canUndo()) {
                index -= 1;
            }

            return this.current();
        },
        redo() {
            if (this.canRedo()) {
                index += 1;
            }

            return this.current();
        },
    };
}
