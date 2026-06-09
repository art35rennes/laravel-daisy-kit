export default function init(root) {
  root.querySelectorAll('[data-copy-button]').forEach((button) => {
    button.addEventListener('click', async () => {
      const targetId = button.dataset.copyTarget;
      let text = button.dataset.copyText;

      if (!text && targetId) {
        const target = document.getElementById(targetId);
        text = target?.textContent || target?.innerText || '';
      }

      if (!text) {
        return;
      }

      try {
        await navigator.clipboard.writeText(text.trim());

        const copyText = button.querySelector('.copy-text');

        if (copyText) {
          const original = copyText.textContent;

          copyText.textContent = 'Copie!';
          setTimeout(() => {
            copyText.textContent = original;
          }, 2000);
        }
      } catch (error) {
        console.error('Erreur lors de la copie:', error);
      }
    });
  });
}
