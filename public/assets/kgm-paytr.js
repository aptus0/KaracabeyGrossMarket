window.addEventListener('DOMContentLoaded', function () {
  var frame = document.getElementById('paytriframe');

  if (!frame || typeof window.iFrameResize !== 'function') {
    return;
  }

  window.iFrameResize({}, '#paytriframe');
});
