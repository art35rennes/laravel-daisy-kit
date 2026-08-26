let fallbackSequence = 0;

function fallbackPart() {
    fallbackSequence += 1;

    return `${Date.now().toString(36)}-${fallbackSequence.toString(36)}-${Math.random().toString(36).slice(2)}`;
}

/**
 * Generates a DOM-safe, instance-local identifier without requiring a secure origin.
 *
 * Web Crypto improves uniqueness where it is available. The fallback deliberately avoids
 * depending on Web Crypto because HTTP development hosts do not expose randomUUID().
 */
export function createInstanceIdentifier(prefix) {
    const randomUUID = globalThis.crypto?.randomUUID;
    const uniquePart = typeof randomUUID === 'function'
        ? randomUUID.call(globalThis.crypto).replaceAll('-', '')
        : fallbackPart();

    return `${prefix}-${uniquePart}`;
}
