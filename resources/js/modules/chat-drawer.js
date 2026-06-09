export default function init(root, options = {}) {
  const drawerToggle = document.getElementById(options.drawerId || 'chat-sidebar-drawer');

  root.querySelectorAll('.chat-sidebar [data-conversation-id]').forEach((row) => {
    row.addEventListener('click', () => {
      if (window.innerWidth < 1024 && drawerToggle) {
        drawerToggle.checked = false;
      }
    });
  });
}
