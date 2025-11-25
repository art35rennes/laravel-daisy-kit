/**
 * Wrapper pour le sous-module chat-widget
 */
import { initChatWidget } from './index.js';

export default function init(root, options = {}) {
    return initChatWidget(root, options);
}

