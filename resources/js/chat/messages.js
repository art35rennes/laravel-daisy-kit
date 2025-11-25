/**
 * Wrapper pour le sous-module chat-messages
 */
import { initChatMessages } from './index.js';

export default function init(root, options = {}) {
    return initChatMessages(root, options);
}

