document.addEventListener('DOMContentLoaded', () => {
  const toggleButtons = document.querySelectorAll('.toggle-btn');
  const viewPanels = document.querySelectorAll('.view-panel');

  toggleButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetView = btn.getAttribute('data-view');

      // Update active toggle button
      toggleButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Update active view panel
      viewPanels.forEach(panel => {
        panel.classList.remove('active');
        if (panel.id === `view-${targetView}`) {
          panel.classList.add('active');
        }
      });
    });
  });

  // Action Buttons Feedback
  const mobileButtons = document.querySelectorAll('.mobile-action-btn, .btn-sm');
  mobileButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const originalText = btn.innerHTML;
      btn.innerHTML = '✓ Diproses...';
      btn.style.opacity = '0.7';
      setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.opacity = '1';
      }, 1000);
    });
  });
});
