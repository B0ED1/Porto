/**
 * BeatPulse - Visual Engine & Relaxed Aesthetic Visualizer
 * Smooth, clean, lo-fi aesthetic canvas renderer with ambient glowing particles,
 * soft pastel radial spectrum equalizer, and clean floaters.
 */

export class VisualEngine {
  constructor(canvas) {
    this.canvas = canvas;
    this.ctx = canvas.getContext('2d');

    this.particles = [];
    this.shockwaves = [];
    this.hitFloaters = [];
    this.ambientDust = [];
    this.screenShake = 0;

    this.resize();
    this.initAmbientDust();
    window.addEventListener('resize', () => this.resize());
  }

  resize() {
    this.width = this.canvas.width = window.innerWidth;
    this.height = this.canvas.height = window.innerHeight;
    this.centerX = this.width / 2;
    this.centerY = this.height / 2;
  }

  initAmbientDust() {
    this.ambientDust = [];
    for (let i = 0; i < 40; i++) {
      this.ambientDust.push({
        x: Math.random() * this.width,
        y: Math.random() * this.height,
        radius: Math.random() * 3 + 1,
        vy: -Math.random() * 0.5 - 0.2,
        alpha: Math.random() * 0.5 + 0.2
      });
    }
  }

  triggerShake(intensity = 8) {
    this.screenShake = intensity;
  }

  addHitParticle(x, y, color = '#2dd4bf', count = 16) {
    for (let i = 0; i < count; i++) {
      const angle = Math.random() * Math.PI * 2;
      const speed = Math.random() * 7 + 2;
      this.particles.push({
        x,
        y,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        radius: Math.random() * 4 + 2,
        color,
        alpha: 1.0,
        decay: Math.random() * 0.025 + 0.02
      });
    }

    this.shockwaves.push({
      x,
      y,
      radius: 8,
      maxRadius: 60,
      color,
      alpha: 1.0,
      lineWidth: 3
    });
  }

  addFloatingText(x, y, text, color = '#c084fc') {
    this.hitFloaters.push({
      x,
      y,
      text,
      color,
      vy: -2,
      alpha: 1.0,
      decay: 0.025
    });
  }

  clear() {
    this.ctx.save();
    if (this.screenShake > 0) {
      const offsetX = (Math.random() - 0.5) * this.screenShake;
      const offsetY = (Math.random() - 0.5) * this.screenShake;
      this.ctx.translate(offsetX, offsetY);
      this.screenShake *= 0.86;
      if (this.screenShake < 0.5) this.screenShake = 0;
    }

    this.ctx.fillStyle = '#0f172a';
    this.ctx.fillRect(-20, -20, this.width + 40, this.height + 40);
  }

  /**
   * Draws soft ambient background glow and floating bokeh particles
   */
  drawAudioReactiveBackground(audioEngine) {
    const bass = audioEngine ? audioEngine.bassEnergy : 0.2;
    const mid = audioEngine ? audioEngine.midEnergy : 0.2;

    // Soft Radial Gradient
    const radius = Math.max(this.width, this.height) * (0.35 + bass * 0.25);
    const grad = this.ctx.createRadialGradient(
      this.centerX, this.centerY, 10,
      this.centerX, this.centerY, radius
    );

    const purpleGlow = `rgba(192, 132, 252, ${0.08 + bass * 0.15})`;
    const tealGlow = `rgba(45, 212, 191, ${0.05 + mid * 0.12})`;

    grad.addColorStop(0, purpleGlow);
    grad.addColorStop(0.6, tealGlow);
    grad.addColorStop(1, 'rgba(15, 23, 42, 0.98)');

    this.ctx.fillStyle = grad;
    this.ctx.fillRect(0, 0, this.width, this.height);

    // Floating Ambient Dust Particles
    this.drawAmbientDust(bass);

    // Radial Spectrum Equalizer Ring
    if (audioEngine && audioEngine.frequencyData) {
      this.drawSpectrumRing(audioEngine.frequencyData, bass);
    }
  }

  drawAmbientDust(bass) {
    this.ctx.save();
    for (const d of this.ambientDust) {
      d.y += d.vy - (bass * 0.5);
      if (d.y < -10) {
        d.y = this.height + 10;
        d.x = Math.random() * this.width;
      }

      this.ctx.globalAlpha = d.alpha;
      this.ctx.fillStyle = '#c084fc';
      this.ctx.beginPath();
      this.ctx.arc(d.x, d.y, d.radius, 0, Math.PI * 2);
      this.ctx.fill();
    }
    this.ctx.restore();
  }

  /**
   * Draws smooth pastel spectrum equalizer bars
   */
  drawSpectrumRing(freqData, bass) {
    const bars = 64;
    const baseRadius = Math.min(this.width, this.height) * 0.23 + (bass * 20);
    const step = (Math.PI * 2) / bars;

    this.ctx.save();
    this.ctx.translate(this.centerX, this.centerY);

    for (let i = 0; i < bars; i++) {
      const val = freqData[i * 2] / 255;
      const barHeight = val * 75 + 5;
      const angle = i * step;

      const x1 = Math.cos(angle) * baseRadius;
      const y1 = Math.sin(angle) * baseRadius;
      const x2 = Math.cos(angle) * (baseRadius + barHeight);
      const y2 = Math.sin(angle) * (baseRadius + barHeight);

      // Smooth Pastel Rainbow Spectrum
      const hue = (i / bars) * 260 + 170; // Soft Teal -> Lavender -> Coral
      this.ctx.strokeStyle = `hsla(${hue}, 85%, 70%, ${0.5 + val * 0.5})`;
      this.ctx.lineWidth = 3;

      this.ctx.beginPath();
      this.ctx.moveTo(x1, y1);
      this.ctx.lineTo(x2, y2);
      this.ctx.stroke();
    }

    this.ctx.restore();
  }

  /**
   * Render and update active particles, shockwaves, and floating text
   */
  updateAndDrawParticles() {
    // Shockwaves
    for (let i = this.shockwaves.length - 1; i >= 0; i--) {
      const sw = this.shockwaves[i];
      sw.radius += 4;
      sw.alpha -= 0.04;

      if (sw.alpha <= 0 || sw.radius >= sw.maxRadius) {
        this.shockwaves.splice(i, 1);
        continue;
      }

      this.ctx.save();
      this.ctx.globalAlpha = sw.alpha;
      this.ctx.strokeStyle = sw.color;
      this.ctx.lineWidth = sw.lineWidth;
      this.ctx.beginPath();
      this.ctx.arc(sw.x, sw.y, sw.radius, 0, Math.PI * 2);
      this.ctx.stroke();
      this.ctx.restore();
    }

    // Particles
    for (let i = this.particles.length - 1; i >= 0; i--) {
      const p = this.particles[i];
      p.x += p.vx;
      p.y += p.vy;
      p.alpha -= p.decay;

      if (p.alpha <= 0) {
        this.particles.splice(i, 1);
        continue;
      }

      this.ctx.save();
      this.ctx.globalAlpha = p.alpha;
      this.ctx.fillStyle = p.color;
      this.ctx.beginPath();
      this.ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
      this.ctx.fill();
      this.ctx.restore();
    }

    // Hit Floating Text
    for (let i = this.hitFloaters.length - 1; i >= 0; i--) {
      const f = this.hitFloaters[i];
      f.y += f.vy;
      f.alpha -= f.decay;

      if (f.alpha <= 0) {
        this.hitFloaters.splice(i, 1);
        continue;
      }

      this.ctx.save();
      this.ctx.globalAlpha = f.alpha;
      this.ctx.fillStyle = f.color;
      this.ctx.font = '700 24px Outfit';
      this.ctx.textAlign = 'center';
      this.ctx.fillText(f.text, f.x, f.y);
      this.ctx.restore();
    }

    this.ctx.restore();
  }
}
