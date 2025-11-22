/**
 * Wrapper pour le sous-module chat-messages
 */
import { initChatMessages } from './chat.js';

export default function init(root, options = {}) {
    return initChatMessages(root, options);
}


