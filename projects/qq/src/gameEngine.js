/**
 * BeatPulse - Game Engine Core
 * Handles game state, score tracking, combo multiplier, input listeners,
 * mode switching, sound effects, and main requestAnimationFrame loop.
 */

import { BeatCatcherMode } from './modes/beatCatcher.js';
import { RhythmRunnerMode } from './modes/rhythmRunner.js';

export class GameEngine {
  constructor(canvas, audioEngine, visualEngine) {
    this.canvas = canvas;
    this.audio = audioEngine;
    this.visuals = visualEngine;

    this.state = 'MENU'; // 'MENU' | 'PLAYING' | 'PAUSED' | 'GAME_OVER'
    this.selectedMode = 'CATCHER'; // 'CATCHER' | 'RUNNER'

    this.modeA = new BeatCatcherMode(this);
    this.modeB = new RhythmRunnerMode(this);
    this.activeModeHandler = this.modeA;

    // Gameplay Statistics
    this.score = 0;
    this.combo = 0;
    this.maxCombo = 0;
    this.multiplier = 1;

    this.perfectHits = 0;
    this.greatHits = 0;
    this.goodHits = 0;
    this.misses = 0;

    this.lastTime = 0;
    this.animationFrameId = null;

    this.initInputListeners();
  }

  setMode(modeType) {
    this.selectedMode = modeType;
    if (modeType === 'CATCHER') {
      this.activeModeHandler = this.modeA;
    } else {
      this.activeModeHandler = this.modeB;
    }
  }

  initInputListeners() {
    window.addEventListener('keydown', (e) => {
      // Do not intercept keys if user is typing inside an input box or textarea
      const targetTag = e.target ? e.target.tagName.toLowerCase() : '';
      if (targetTag === 'input' || targetTag === 'textarea' || (e.target && e.target.isContentEditable)) {
        return;
      }

      if (e.code === 'Space') {
        e.preventDefault();
        this.togglePause();
        return;
      }

      if (this.state === 'PLAYING') {
        this.activeModeHandler.handleKeyDown(e.key);
      }
    });

    window.addEventListener('keyup', (e) => {
      const targetTag = e.target ? e.target.tagName.toLowerCase() : '';
      if (targetTag === 'input' || targetTag === 'textarea' || (e.target && e.target.isContentEditable)) {
        return;
      }

      if (this.state === 'PLAYING' && this.activeModeHandler.handleKeyUp) {
        this.activeModeHandler.handleKeyUp(e.key);
      }
    });

    // Touch / Click Support for Mode A Catcher quadrants
    this.canvas.addEventListener('click', (e) => {
      if (this.state !== 'PLAYING') return;

      const rect = this.canvas.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const clickY = e.clientY - rect.top;

      if (this.selectedMode === 'CATCHER') {
        const dx = clickX - this.visuals.centerX;
        const dy = clickY - this.visuals.centerY;

        let key = 'ArrowRight';
        if (Math.abs(dx) > Math.abs(dy)) {
          key = dx > 0 ? 'ArrowRight' : 'ArrowLeft';
        } else {
          key = dy > 0 ? 'ArrowDown' : 'ArrowUp';
        }
        this.modeA.handleKeyDown(key);
      }
    });
  }

  start() {
    this.score = 0;
    this.combo = 0;
    this.maxCombo = 0;
    this.multiplier = 1;
    this.perfectHits = 0;
    this.greatHits = 0;
    this.goodHits = 0;
    this.misses = 0;

    this.activeModeHandler.reset();
    this.state = 'PLAYING';
    this.audio.play();

    this.lastTime = performance.now();
    if (!this.animationFrameId) {
      this.loop(this.lastTime);
    }
  }

  togglePause() {
    if (this.state === 'PLAYING') {
      this.state = 'PAUSED';
      this.audio.pause();
    } else if (this.state === 'PAUSED') {
      this.state = 'PLAYING';
      this.audio.resume();
      this.lastTime = performance.now();
    }
  }

  stop() {
    this.state = 'MENU';
    this.audio.stop();
  }

  registerHit(x, y, basePoints, rating, color = '#00f0ff') {
    this.combo++;
    if (this.combo > this.maxCombo) this.maxCombo = this.combo;

    if (this.combo >= 50) this.multiplier = 8;
    else if (this.combo >= 25) this.multiplier = 4;
    else if (this.combo >= 10) this.multiplier = 2;
    else this.multiplier = 1;

    const gainedPoints = basePoints * this.multiplier;
    this.score += gainedPoints;

    if (rating === 'PERFECT') this.perfectHits++;
    else if (rating === 'GREAT') this.greatHits++;
    else if (rating === 'GOOD') this.goodHits++;

    this.audio.playHitSFX(rating);
    this.visuals.addHitParticle(x, y, color, rating === 'PERFECT' ? 24 : 12);
    this.visuals.addFloatingText(x, y - 10, `+${gainedPoints} ${rating}!`, rating === 'PERFECT' ? '#ffe600' : '#00f0ff');

    if (this.combo > 0 && this.combo % 10 === 0) {
      this.visuals.triggerShake(12);
    }
  }

  registerMiss(x, y) {
    this.combo = 0;
    this.multiplier = 1;
    this.misses++;

    this.audio.playMissSFX();
    this.visuals.addFloatingText(x, y, 'MISS!', '#ff0055');
  }

  saveHighScore() {
    const key = `beatpulse_highscore_${this.selectedMode}_${this.audio.trackName}`;
    const prevHigh = parseInt(localStorage.getItem(key) || '0', 10);
    if (this.score > prevHigh) {
      localStorage.setItem(key, this.score.toString());
      return true;
    }
    return false;
  }

  getHighScore() {
    const key = `beatpulse_highscore_${this.selectedMode}_${this.audio.trackName}`;
    return parseInt(localStorage.getItem(key) || '0', 10);
  }

  loop(currentTime) {
    const deltaTime = Math.min((currentTime - this.lastTime) / 1000, 0.1);
    this.lastTime = currentTime;

    if (this.state === 'PLAYING') {
      this.audio.update();
      this.activeModeHandler.update(deltaTime);

      if (this.audio.duration > 0 && this.audio.getCurrentTime() >= this.audio.duration - 0.5) {
        this.state = 'GAME_OVER';
        this.saveHighScore();
        if (this.onGameOver) this.onGameOver();
      }
    }

    this.visuals.clear();
    this.visuals.drawAudioReactiveBackground(this.audio);

    if (this.state === 'PLAYING' || this.state === 'PAUSED') {
      this.activeModeHandler.render(this.visuals.ctx, this.visuals);
    }

    this.visuals.updateAndDrawParticles();

    if (this.onHUDUpdate) {
      this.onHUDUpdate({
        score: this.score,
        combo: this.combo,
        multiplier: this.multiplier,
        progress: this.audio.duration ? (this.audio.getCurrentTime() / this.audio.duration) * 100 : 0
      });
    }

    this.animationFrameId = requestAnimationFrame((t) => this.loop(t));
  }
}
