/**
 * Wrapper pour le sous-module chat-input
 */
import { initChatInput } from '../chat/index.js';

export default function init(root, options = {}) {
    return initChatInput(root, options);
}


