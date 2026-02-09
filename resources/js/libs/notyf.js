// src/notyf.js
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
  duration: 3000,
  position: { x: 'center', y: 'bottom' },
});

export default notyf;