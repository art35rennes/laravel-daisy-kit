/**
 * Wrapper pour le sous-module chat-widget
 */
import { initChatWidget } from '../chat/index.js';

export default function init(root, options = {}) {
    return initChatWidget(root, options);
}


