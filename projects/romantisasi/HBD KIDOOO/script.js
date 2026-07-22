// Terminal Typing Sequence Lines
const terminalLines = [
  { text: "[SYSTEM] Initializing core affection protocols...", delay: 600 },
  { text: "[SYSTEM] Scanning memory sectors... 2,458 happy memories found.", delay: 800 },
  { text: "[SYSTEM] Establishing emotional uplink... SECURE", delay: 900 },
  { text: "[SYSTEM] Overriding logical blocks... 100% SUCCESS", delay: 700 },
  { text: "[SYSTEM] Calibrating heartbeat sync... STABLE (72 BPM)", delay: 800 },
  { text: "[SYSTEM] Generating decryption key: PRETTIEST_GIRL_KIDOOO", delay: 900 },
  { text: "[SYSTEM] Boot complete. Personal message is ready to decrypt.", delay: 500 }
];

let phase = 'boot'; // boot, ready, animating, message
const mouse = { x: null, y: null, radius: 100 };

// Sound effect and music handling
const bgMusic = document.getElementById('bg-music');

// Register Event Listeners
window.addEventListener('load', () => {
  runTerminalBoot();
  
  // Set up canvases
  resizeCanvases();
  window.addEventListener('resize', resizeCanvases);
});

window.addEventListener('mousemove', (e) => {
  mouse.x = e.clientX;
  mouse.y = e.clientY;
});

window.addEventListener('mouseleave', () => {
  mouse.x = null;
  mouse.y = null;
});

// Setup Canvas Contexts
const heartCanvas = document.getElementById('heart-canvas');
const hctx = heartCanvas.getContext('2d');

const ambientCanvas = document.getElementById('ambient-canvas');
const actx = ambientCanvas.getContext('2d');

function resizeCanvases() {
  heartCanvas.width = window.innerWidth;
  heartCanvas.height = window.innerHeight;
  ambientCanvas.width = window.innerWidth;
  ambientCanvas.height = window.innerHeight;
  
  // Recalculate heart target points on resize
  if (phase === 'animating' || phase === 'message') {
    recalculateHeartTargets();
  }
}

// 1. Terminal Typing Animation
async function runTerminalBoot() {
  const logContainer = document.getElementById('terminal-log');
  const consoleContainer = document.getElementById('terminal-console');
  
  for (let lineInfo of terminalLines) {
    await typeLine(lineInfo, logContainer, consoleContainer);
  }
  
  // Typing completed - automatically start the affection sequence
  phase = 'ready';
  setTimeout(() => {
    startAffectionSequence();
  }, 1000);
}

function typeLine(lineInfo, container, scrollParent) {
  return new Promise((resolve) => {
    const line = document.createElement('div');
    line.className = 'mb-2 font-mono text-sm md:text-base leading-relaxed';
    
    // Command prompt indicator
    const prompt = document.createElement('span');
    prompt.className = 'text-green-500 mr-2';
    prompt.textContent = '>';
    line.appendChild(prompt);
    
    const textNode = document.createElement('span');
    line.appendChild(textNode);
    container.appendChild(line);
    
    let charIdx = 0;
    const txt = lineInfo.text;
    
    function typeChar() {
      if (charIdx < txt.length) {
        textNode.textContent += txt.charAt(charIdx);
        charIdx++;
        scrollParent.scrollTop = scrollParent.scrollHeight;
        // Natural speed variation
        setTimeout(typeChar, 15 + Math.random() * 20);
      } else {
        setTimeout(resolve, lineInfo.delay);
      }
    }
    
    typeChar();
  });
}

// 2. Action Trigger & Audio Handler
function playBackgroundMusic() {
  const playPromise = bgMusic.play();
  
  const fadeVolume = () => {
    let fadeInterval = setInterval(() => {
      if (bgMusic.volume < 0.95) {
        bgMusic.volume += 0.05;
      } else {
        bgMusic.volume = 1.0;
        clearInterval(fadeInterval);
      }
    }, 150);
  };

  if (playPromise !== undefined) {
    playPromise.then(() => {
      fadeVolume();
    }).catch(err => {
      console.log('Audio autoplay blocked, waiting for user gesture...', err);
      
      const startAudioOnInteraction = () => {
        bgMusic.play().then(() => {
          fadeVolume();
        }).catch(e => console.log('Audio gesture playback failed:', e));
        
        document.removeEventListener('click', startAudioOnInteraction);
        document.removeEventListener('touchstart', startAudioOnInteraction);
        document.removeEventListener('keydown', startAudioOnInteraction);
      };
      
      document.addEventListener('click', startAudioOnInteraction);
      document.addEventListener('touchstart', startAudioOnInteraction);
      document.addEventListener('keydown', startAudioOnInteraction);
    });
  }
}

function startAffectionSequence() {
  phase = 'animating';
  
  // Play background music and fade in volume
  bgMusic.volume = 0;
  playBackgroundMusic();
  
  // Fade out terminal console
  const terminal = document.getElementById('terminal-container');
  terminal.classList.add('fade-out');
  
  // Show Canvas overlays
  heartCanvas.classList.remove('hidden');
  ambientCanvas.classList.remove('hidden');
  heartCanvas.style.opacity = '1';
  ambientCanvas.style.opacity = '1';
  
  // Initialize animation engine
  initParticles();
  initFireflies();
  
  // Start loop
  requestAnimationFrame(animationLoop);
  
  // Trigger love message card fade-in after 8.5 seconds
  setTimeout(() => {
    transitionToLoveMessage();
  }, 8500);
}

// 3. Canvas Particle Engine (The Pulsing Heart of Texts)
const particles = [];
const fireflies = [];
const totalParticles = 480;
const particlePhrases = ["I love you", "love", "♡", "you", "forever", "sayang", "selamanya", "my princess"];

// Heart geometry base values
const baseHeartPoints = [];

// Calculate base heart shape coordinates
// Using parametric equations
for (let i = 0; i < totalParticles; i++) {
  const t = (i / totalParticles) * Math.PI * 2;
  const x = 16 * Math.pow(Math.sin(t), 3);
  // Negated to flip canvas Y-axis upwards
  const y = -(13 * Math.cos(t) - 5 * Math.cos(2*t) - 2 * Math.cos(3*t) - Math.cos(4*t));
  baseHeartPoints.push({ x, y });
}

class Particle {
  constructor(tx, ty, text) {
    this.targetX = tx;
    this.targetY = ty;
    this.text = text;
    
    // Spawn off-screen
    const angle = Math.random() * Math.PI * 2;
    const distance = Math.max(window.innerWidth, window.innerHeight) * (0.8 + Math.random() * 0.4);
    this.x = window.innerWidth / 2 + Math.cos(angle) * distance;
    this.y = window.innerHeight / 2 + Math.sin(angle) * distance;
    
    this.vx = 0;
    this.vy = 0;
    this.speed = 0.6 + Math.random() * 0.8;
    this.seed = Math.random() * 100;
    
    // Size and styling
    this.size = Math.floor(Math.random() * 5) + 9; // 9px to 14px
    this.baseHue = Math.random() > 0.5 ? 340 : 355; // Red/Pink spectrum
    this.hueShift = Math.random() * 15;
    this.alpha = 0.15 + Math.random() * 0.75;
  }
  
  update(scaleFactor, time, isAssembled) {
    // 1. Hover ripple effect (Mouse interaction)
    let extraX = 0;
    let extraY = 0;
    
    if (mouse.x !== null && mouse.y !== null) {
      const dx = this.x - mouse.x;
      const dy = this.y - mouse.y;
      const dist = Math.sqrt(dx*dx + dy*dy);
      
      if (dist < mouse.radius) {
        const force = (mouse.radius - dist) / mouse.radius;
        // Add velocity pushing away
        this.vx += (dx / dist) * force * 1.5;
        this.vy += (dy / dist) * force * 1.5;
      }
    }
    
    // 2. Heart attraction physics
    const currentTargetX = window.innerWidth / 2 + this.targetX * scaleFactor;
    const currentTargetY = window.innerHeight / 2 + this.targetY * scaleFactor;
    
    const dx = currentTargetX - this.x;
    const dy = currentTargetY - this.y;
    
    // Easing factor increases as time goes by to tighten assembly
    const pullSpeed = isAssembled ? 0.08 : 0.03;
    
    // Add subtle ambient floating wave using seed
    const driftX = Math.sin(time * 2 + this.seed) * 0.4;
    const driftY = Math.cos(time * 2 + this.seed) * 0.4;
    
    this.vx += dx * pullSpeed * this.speed + driftX;
    this.vy += dy * pullSpeed * this.speed + driftY;
    
    // Friction
    this.vx *= 0.85;
    this.vy *= 0.85;
    
    this.x += this.vx;
    this.y += this.vy;
  }
  
  draw(ctx, time) {
    // Glowing color spectrum shifting over time
    const hue = (this.baseHue + this.hueShift + Math.sin(time * 1.5) * 15) % 360;
    ctx.shadowBlur = 8;
    ctx.shadowColor = `hsla(${hue}, 95%, 60%, 0.6)`;
    ctx.fillStyle = `hsla(${hue}, 95%, 65%, ${this.alpha})`;
    ctx.font = `${this.size}px 'Share Tech Mono', monospace`;
    ctx.fillText(this.text, this.x, this.y);
  }
}

// Fireflies Background Engine
class Firefly {
  constructor() {
    this.x = Math.random() * window.innerWidth;
    this.y = Math.random() * window.innerHeight;
    this.size = Math.random() * 2 + 1.2;
    this.speedY = -(Math.random() * 0.3 + 0.15); // Drifting upwards
    this.speedX = Math.random() * 0.2 - 0.1;
    this.alpha = Math.random() * 0.7 + 0.2;
    this.alphaSpeed = Math.random() * 0.015 + 0.005;
    this.angle = Math.random() * Math.PI * 2;
    this.angleSpeed = Math.random() * 0.02 + 0.01;
  }
  
  update() {
    this.y += this.speedY;
    this.angle += this.angleSpeed;
    this.x += Math.sin(this.angle) * 0.25 + this.speedX;
    
    // Breathe twinkle
    this.alpha += this.alphaSpeed;
    if (this.alpha > 0.9 || this.alpha < 0.2) {
      this.alphaSpeed = -this.alphaSpeed;
    }
    
    // Wrap around screen boundaries
    if (this.y < -10) {
      this.y = window.innerHeight + 10;
      this.x = Math.random() * window.innerWidth;
    }
    if (this.x < -10 || this.x > window.innerWidth + 10) {
      this.x = Math.random() * window.innerWidth;
    }
  }
  
  draw(ctx) {
    ctx.save();
    ctx.globalAlpha = this.alpha;
    ctx.shadowBlur = 12;
    ctx.shadowColor = "rgba(255, 236, 150, 0.85)";
    ctx.fillStyle = "rgba(255, 236, 150, 0.7)";
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();
  }
}

function initParticles() {
  particles.length = 0;
  for (let i = 0; i < totalParticles; i++) {
    const pt = baseHeartPoints[i];
    const phrase = particlePhrases[i % particlePhrases.length];
    particles.push(new Particle(pt.x, pt.y, phrase));
  }
}

function recalculateHeartTargets() {
  // Simple re-assignment of target reference coords
  for (let i = 0; i < totalParticles; i++) {
    particles[i].targetX = baseHeartPoints[i].x;
    particles[i].targetY = baseHeartPoints[i].y;
  }
}

function initFireflies() {
  fireflies.length = 0;
  const count = Math.floor((window.innerWidth * window.innerHeight) / 25000); // Responsive count
  const safeCount = Math.max(25, Math.min(count, 65));
  for (let i = 0; i < safeCount; i++) {
    fireflies.push(new Firefly());
  }
}

// 4. Main Animation Loop
let animationStartTime = 0;

function animationLoop(timestamp) {
  if (!animationStartTime) animationStartTime = timestamp;
  const elapsed = (timestamp - animationStartTime) / 1000;
  
  // Clear contexts
  hctx.clearRect(0, 0, heartCanvas.width, heartCanvas.height);
  actx.clearRect(0, 0, ambientCanvas.width, ambientCanvas.height);
  
  // Render ambient fireflies
  for (let f of fireflies) {
    f.update();
    f.draw(actx);
  }
  
  // Check if particles have converged/assembled
  // They start off-screen and assemble mostly within 4 seconds
  const isAssembled = elapsed > 4.2;
  
  // Heartbeat pulse calculation
  let pulse = 0;
  if (isAssembled) {
    // 72 BPM heartbeat wave logic (approx 1.2Hz)
    // Double pulse pattern
    const pulseTime = elapsed * 3.8;
    const wave = Math.sin(pulseTime);
    pulse = wave > 0 ? wave * 0.08 : wave * 0.02;
  }
  
  // Responsive base heart scale
  const baseScale = Math.min(window.innerWidth, window.innerHeight) * 0.014;
  const currentScale = baseScale * (1.0 + pulse);
  
  // Align text settings
  hctx.textAlign = 'center';
  hctx.textBaseline = 'middle';
  
  // Update & Draw heart particles
  for (let p of particles) {
    p.update(currentScale, elapsed, isAssembled);
    p.draw(hctx, elapsed);
  }
  
  requestAnimationFrame(animationLoop);
}

// 5. Letter Transition (Easter Egg card fade-in)
function transitionToLoveMessage() {
  phase = 'message';
  
  // Soften the opacity of the heart canvas so overlaid text is legible
  heartCanvas.style.transition = 'opacity 3s ease';
  heartCanvas.style.opacity = '0.3';
  
  // Fade in the letter panel
  const msgContainer = document.getElementById('message-container');
  msgContainer.classList.remove('hidden');
  // Trigger CSS redraw for transition
  void msgContainer.offsetWidth;
  msgContainer.classList.add('fade-in');
}

// 6. Close and Reopen message interactions
const msgContainer = document.getElementById('message-container');
const closeMsgBtn = document.getElementById('close-msg-btn');
const reopenMsgBtn = document.getElementById('reopen-msg-btn');

closeMsgBtn.addEventListener('click', () => {
  // Fade out message card
  msgContainer.classList.add('fade-out');
  msgContainer.classList.remove('fade-in');
  
  // Restore heart canvas opacity back to full (makes it beautiful to see the full particle heart)
  heartCanvas.style.opacity = '1';
  
  // Clean classes after transition
  setTimeout(() => {
    msgContainer.classList.add('hidden');
    msgContainer.classList.remove('fade-out');
  }, 1500);
  
  // Fade in reopen floating button
  reopenMsgBtn.classList.remove('hidden');
  void reopenMsgBtn.offsetWidth;
  reopenMsgBtn.classList.add('fade-in');
  reopenMsgBtn.style.opacity = '1';
});

reopenMsgBtn.addEventListener('click', () => {
  // Fade out reopen button
  reopenMsgBtn.classList.add('fade-out');
  reopenMsgBtn.classList.remove('fade-in');
  reopenMsgBtn.style.opacity = '0';
  
  setTimeout(() => {
    reopenMsgBtn.classList.add('hidden');
    reopenMsgBtn.classList.remove('fade-out');
  }, 500);
  
  // Lower heart canvas opacity
  heartCanvas.style.opacity = '0.3';
  
  // Fade in message card
  msgContainer.classList.remove('hidden');
  void msgContainer.offsetWidth;
  msgContainer.classList.add('fade-in');
});
