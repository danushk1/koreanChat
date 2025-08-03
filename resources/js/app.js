import './bootstrap';
import FingerprintJS from '@fingerprintjs/fingerprintjs';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

let visitorId = null;
FingerprintJS.load().then(fp => {
  fp.get().then(result => {
    visitorId = result.visitorId;
    localStorage.setItem('fingerprint_id', visitorId);
  });
});