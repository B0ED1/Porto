/**
 * BeatPulse - Dynamic Audio Engine & Preset Library
 * Handles Web Audio API setup, real-time frequency analysis, offline beat-map generation,
 * online audio stream fetching with full-length 3-minute looping extension, and rhythm SFX synthesis.
 */

export class AudioEngine {
  constructor() {
    this.ctx = null;
    this.analyser = null;
    this.sourceNode = null;
    this.audioBuffer = null;

    this.isPlaying = false;
    this.isPaused = false;
    this.startTime = 0;
    this.pauseOffset = 0;
    this.duration = 180; // Default 3-minute full game round
    this.trackName = 'No Track Loaded';

    // Procedural Synth Loop properties
    this.isSynthMode = false;
    this.synthInterval = null;
    this.synthBpm = 120;
    this.synthStep = 0;
    this.activePresetKey = 'lofi';

    // Pre-calculated Beat Map (timestamps in seconds)
    this.beatMap = [];
    this.nextBeatIndex = 0;

    // Real-time Frequency analysis buffers
    this.fftSize = 256;
    this.frequencyData = new Uint8Array(128);

    // Dynamic Adaptive Beat Detection state
    this.bassEnergy = 0;
    this.midEnergy = 0;
    this.trebleEnergy = 0;
    this.energyHistory = [];
    this.historySize = 30;
    this.beatFrameCounter = 0;
    this.isBeatDetected = false;

    // SFX Settings
    this.sfxMuted = false;

    // Callbacks
    this.onBeat = null;
    this.onEnded = null;

    // Rich Preset Song Library Definition
    this.synthPresets = {
      'lofi': {
        name: '☕ Lo-Fi Chill Hop',
        genre: 'Chill / Lo-Fi',
        bpm: 85,
        kickSteps: [0, 8, 10],
        snareSteps: [4, 12],
        bassNotes: [130.81, 130.81, 164.81, 146.83, 130.81, 110, 146.83, 123.47]
      },
      'cyberpulse': {
        name: '🎵 Cyberpulse Synthwave',
        genre: 'Synthwave',
        bpm: 124,
        kickSteps: [0, 4, 8, 12],
        snareSteps: [4, 12],
        bassNotes: [110, 110, 130, 110, 146.83, 110, 130, 98]
      },
      'retrobass': {
        name: '🎸 Retro Electro Bass',
        genre: 'Funk / Electro',
        bpm: 128,
        kickSteps: [0, 6, 10, 12],
        snareSteps: [4, 12],
        bassNotes: [98, 98, 110, 123.47, 98, 87.31, 98, 110]
      },
      'hyperbeat': {
        name: '⚡ Hyperbeat Arcade',
        genre: 'Chiptune / 8-Bit',
        bpm: 140,
        kickSteps: [0, 2, 8, 10, 14],
        snareSteps: [4, 12],
        bassNotes: [174.61, 174.61, 220, 196, 174.61, 146.83, 196, 164.81]
      },
      'midnight': {
        name: '🌊 Midnight Lofi Study',
        genre: 'Ambient Chill',
        bpm: 90,
        kickSteps: [0, 10],
        snareSteps: [4, 12],
        bassNotes: [146.83, 146.83, 164.81, 130.81, 110, 110, 130.81, 146.83]
      },
      'neonpop': {
        name: '🎹 Neon Pop Disco',
        genre: 'Disco Pop',
        bpm: 120,
        kickSteps: [0, 4, 8, 12],
        snareSteps: [4, 12],
        bassNotes: [130.81, 146.83, 164.81, 174.61, 196, 174.61, 164.81, 146.83]
      },
      'cosmic': {
        name: '🚀 Cosmic Dream Pop',
        genre: 'Dreamscape',
        bpm: 110,
        kickSteps: [0, 8, 12],
        snareSteps: [4, 12],
        bassNotes: [110, 130.81, 146.83, 164.81, 130.81, 110, 98, 110]
      },
      'edm': {
        name: '🔥 EDM Beat Drop',
        genre: 'Electronic / Dance',
        bpm: 132,
        kickSteps: [0, 4, 8, 12],
        snareSteps: [4, 12],
        bassNotes: [87.31, 87.31, 98, 110, 87.31, 98, 110, 130.81]
      }
    };
  }

  /**
   * Initializes the Audio Context
   */
  init() {
    if (!this.ctx) {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      this.ctx = new AudioCtx();
      this.analyser = this.ctx.createAnalyser();
      this.analyser.fftSize = this.fftSize;
      this.analyser.smoothingTimeConstant = 0.8;
      this.frequencyData = new Uint8Array(this.analyser.frequencyBinCount);
    }
    if (this.ctx.state === 'suspended') {
      this.ctx.resume();
    }
  }

  /**
   * Fetches an online audio stream URL and extends it to a FULL 3-MINUTE GAME ROUND!
   */
  async loadAudioFromUrl(streamUrl, trackTitle = 'Online Track', targetDurationSeconds = 180) {
    this.init();
    this.stop();
    this.isSynthMode = false;

    try {
      const res = await fetch(streamUrl);
      const arrayBuffer = await res.arrayBuffer();
      const rawBuffer = await this.ctx.decodeAudioData(arrayBuffer);

      // Extend audio buffer seamlessly so the song plays for full targetDurationSeconds (3 minutes)
      this.audioBuffer = this.createExtendedLoopBuffer(rawBuffer, targetDurationSeconds);
      this.duration = targetDurationSeconds;
      this.trackName = trackTitle;

      this.beatMap = this.generateBeatMap(this.audioBuffer);
      this.nextBeatIndex = 0;
      return true;
    } catch (err) {
      console.error('Failed to load audio from URL:', err);
      return false;
    }
  }

  /**
   * Creates a seamless multi-pass audio buffer extending any audio slice to full length (e.g. 180s)
   */
  createExtendedLoopBuffer(sourceBuf, targetDurationSec) {
    const numChannels = sourceBuf.numberOfChannels;
    const sampleRate = sourceBuf.sampleRate;
    const targetSamples = Math.floor(sampleRate * targetDurationSec);
    const sourceSamples = sourceBuf.length;

    const newBuf = this.ctx.createBuffer(numChannels, targetSamples, sampleRate);

    for (let c = 0; c < numChannels; c++) {
      const srcData = sourceBuf.getChannelData(c);
      const destData = newBuf.getChannelData(c);

      let written = 0;
      while (written < targetSamples) {
        const copyLen = Math.min(sourceSamples, targetSamples - written);
        destData.set(srcData.subarray(0, copyLen), written);
        written += copyLen;
      }
    }

    return newBuf;
  }

  /**
   * Loads a local audio ArrayBuffer (MP3 / WAV)
   */
  async loadAudioBuffer(arrayBuffer, name = 'Custom Track') {
    this.init();
    this.stop();
    this.isSynthMode = false;

    try {
      this.audioBuffer = await this.ctx.decodeAudioData(arrayBuffer);
      this.duration = this.audioBuffer.duration;
      this.trackName = name;

      this.beatMap = this.generateBeatMap(this.audioBuffer);
      this.nextBeatIndex = 0;
      return true;
    } catch (err) {
      console.error('Failed to decode audio data:', err);
      return false;
    }
  }

  /**
   * Generates beat map from raw PCM audio channel data
   */
  generateBeatMap(buffer) {
    const pcm = buffer.getChannelData(0);
    const sampleRate = buffer.sampleRate;
    const windowSize = Math.floor(sampleRate * 0.025);
    const beats = [];

    const energyHistory = [];
    const historyLen = 25;

    for (let i = 0; i < pcm.length; i += windowSize) {
      let sum = 0;
      const end = Math.min(i + windowSize, pcm.length);
      for (let j = i; j < end; j++) {
        sum += pcm[j] * pcm[j];
      }
      const rms = Math.sqrt(sum / (end - i));

      energyHistory.push(rms);
      if (energyHistory.length > historyLen) energyHistory.shift();

      const avgEnergy = energyHistory.reduce((a, b) => a + b, 0) / energyHistory.length;

      if (rms > avgEnergy * 1.35 && rms > 0.05) {
        const timestamp = i / sampleRate;
        if (beats.length === 0 || (timestamp - beats[beats.length - 1]) > 0.22) {
          beats.push(timestamp);
        }
      }
    }

    return beats;
  }

  play(offset = 0) {
    if (!this.ctx) this.init();

    if (this.isSynthMode) {
      this.startSynthLoop();
      return;
    }

    if (!this.audioBuffer) return;

    this.stopSource();

    this.sourceNode = this.ctx.createBufferSource();
    this.sourceNode.buffer = this.audioBuffer;
    this.sourceNode.connect(this.analyser);
    this.analyser.connect(this.ctx.destination);

    this.startTime = this.ctx.currentTime - offset;
    this.pauseOffset = offset;
    this.nextBeatIndex = 0;
    this.sourceNode.start(0, offset);
    this.isPlaying = true;
    this.isPaused = false;

    this.sourceNode.onended = () => {
      if (this.isPlaying && !this.isPaused) {
        this.isPlaying = false;
        if (this.onEnded) this.onEnded();
      }
    };
  }

  pause() {
    if (!this.isPlaying) return;
    if (this.isSynthMode) {
      this.stopSynthLoop();
      this.isPaused = true;
      this.isPlaying = false;
      return;
    }

    this.pauseOffset = this.ctx.currentTime - this.startTime;
    this.stopSource();
    this.isPaused = true;
    this.isPlaying = false;
  }

  resume() {
    if (this.isPaused) {
      this.play(this.pauseOffset);
    }
  }

  stop() {
    this.stopSource();
    this.stopSynthLoop();
    this.isPlaying = false;
    this.isPaused = false;
    this.pauseOffset = 0;
    this.nextBeatIndex = 0;
  }

  stopSource() {
    if (this.sourceNode) {
      try {
        this.sourceNode.stop();
        this.sourceNode.disconnect();
      } catch (e) {}
      this.sourceNode = null;
    }
  }

  getCurrentTime() {
    if (!this.isPlaying && !this.isPaused) return 0;
    if (this.isPaused) return this.pauseOffset;
    if (this.isSynthMode) return (Date.now() - this.startTime) / 1000;
    return this.ctx ? Math.min(this.ctx.currentTime - this.startTime, this.duration) : 0;
  }

  update() {
    if (!this.analyser) return;

    this.analyser.getByteFrequencyData(this.frequencyData);

    let bassSum = 0;
    let midSum = 0;
    let trebleSum = 0;

    const binCount = this.frequencyData.length;
    const bassBins = Math.floor(binCount * 0.15);
    const midBins = Math.floor(binCount * 0.5);

    for (let i = 0; i < binCount; i++) {
      const val = this.frequencyData[i] / 255;
      if (i < bassBins) {
        bassSum += val;
      } else if (i < midBins) {
        midSum += val;
      } else {
        trebleSum += val;
      }
    }

    this.bassEnergy = bassSum / bassBins;
    this.midEnergy = midSum / (midBins - bassBins);
    this.trebleEnergy = trebleSum / (binCount - midBins);

    this.energyHistory.push(this.bassEnergy);
    if (this.energyHistory.length > this.historySize) {
      this.energyHistory.shift();
    }

    const avgBass = this.energyHistory.reduce((a, b) => a + b, 0) / this.energyHistory.length;
    const dynamicThreshold = Math.max(0.25, avgBass * 1.3);

    this.isBeatDetected = false;
    if (this.bassEnergy > dynamicThreshold && this.bassEnergy > 0.15 && this.beatFrameCounter <= 0) {
      this.isBeatDetected = true;
      this.beatFrameCounter = 12;
      if (this.onBeat) this.onBeat(this.bassEnergy);
    } else {
      if (this.beatFrameCounter > 0) {
        this.beatFrameCounter--;
      }
    }
  }

  // ==========================================
  // PROCEDURAL SYNTH MUSIC ENGINE
  // ==========================================

  loadSynthPreset(presetKey = 'lofi') {
    this.stop();
    const preset = this.synthPresets[presetKey] || this.synthPresets['lofi'];
    this.activePresetKey = presetKey;
    this.isSynthMode = true;
    this.trackName = preset.name;
    this.synthBpm = preset.bpm;
    this.duration = 180;
    this.beatMap = [];

    const stepTime = (60 / preset.bpm) / 2;
    for (let t = 0; t < this.duration; t += stepTime) {
      this.beatMap.push(t);
    }
  }

  startSynthLoop() {
    this.init();
    this.isSynthMode = true;
    this.isPlaying = true;
    this.isPaused = false;
    this.startTime = Date.now() - (this.pauseOffset * 1000);
    this.synthStep = 0;

    const preset = this.synthPresets[this.activePresetKey] || this.synthPresets['lofi'];
    const stepTime = (60 / preset.bpm) / 4 * 1000;

    this.stopSynthLoop();
    this.synthInterval = setInterval(() => {
      if (!this.isPlaying) return;
      this.triggerSynthStep(this.synthStep, preset);
      this.synthStep = (this.synthStep + 1) % 16;
    }, stepTime);
  }

  stopSynthLoop() {
    if (this.synthInterval) {
      clearInterval(this.synthInterval);
      this.synthInterval = null;
    }
  }

  triggerSynthStep(step, preset) {
    if (!this.ctx) return;
    const now = this.ctx.currentTime;

    if (preset.kickSteps.includes(step)) {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(140, now);
      osc.frequency.exponentialRampToValueAtTime(0.01, now + 0.22);
      gain.gain.setValueAtTime(0.9, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.22);

      osc.connect(gain);
      gain.connect(this.analyser);
      this.analyser.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.22);
    }

    if (preset.snareSteps.includes(step)) {
      const noiseBuffer = this.createNoiseBuffer();
      const noise = this.ctx.createBufferSource();
      noise.buffer = noiseBuffer;

      const filter = this.ctx.createBiquadFilter();
      filter.type = 'highpass';
      filter.frequency.value = 1200;

      const gain = this.ctx.createGain();
      gain.gain.setValueAtTime(0.5, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.18);

      noise.connect(filter);
      filter.connect(gain);
      gain.connect(this.analyser);
      this.analyser.connect(this.ctx.destination);
      noise.start(now);
      noise.stop(now + 0.18);
    }

    if (step % 2 === 0 && preset.bassNotes) {
      const note = preset.bassNotes[Math.floor(step / 2) % preset.bassNotes.length];
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(note, now);

      gain.gain.setValueAtTime(0.28, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.16);

      osc.connect(gain);
      gain.connect(this.analyser);
      this.analyser.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.16);
    }
  }

  createNoiseBuffer() {
    const bufferSize = this.ctx.sampleRate * 0.18;
    const buffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
    const output = buffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
      output[i] = Math.random() * 2 - 1;
    }
    return buffer;
  }

  // ==========================================
  // RHYTHM-GAME PERCUSSION HIT SFX SYNTHESIZER
  // ==========================================

  playHitSFX(quality = 'PERFECT') {
    if (this.sfxMuted || !this.ctx) return;
    const now = this.ctx.currentTime;

    if (quality === 'PERFECT') {
      const kickOsc = this.ctx.createOscillator();
      const kickGain = this.ctx.createGain();
      kickOsc.type = 'sine';
      kickOsc.frequency.setValueAtTime(150, now);
      kickOsc.frequency.exponentialRampToValueAtTime(35, now + 0.08);

      kickGain.gain.setValueAtTime(0.35, now);
      kickGain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);

      kickOsc.connect(kickGain);
      kickGain.connect(this.ctx.destination);
      kickOsc.start(now);
      kickOsc.stop(now + 0.08);

      const noise = this.ctx.createBufferSource();
      noise.buffer = this.createNoiseBuffer();

      const filter = this.ctx.createBiquadFilter();
      filter.type = 'highpass';
      filter.frequency.value = 4000;

      const noiseGain = this.ctx.createGain();
      noiseGain.gain.setValueAtTime(0.15, now);
      noiseGain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);

      noise.connect(filter);
      filter.connect(noiseGain);
      noiseGain.connect(this.ctx.destination);
      noise.start(now);
      noise.stop(now + 0.05);
    } else if (quality === 'GREAT') {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(750, now);
      osc.frequency.exponentialRampToValueAtTime(200, now + 0.06);

      gain.gain.setValueAtTime(0.2, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.06);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.06);
    } else {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(1100, now);
      osc.frequency.exponentialRampToValueAtTime(550, now + 0.04);

      gain.gain.setValueAtTime(0.15, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.04);
    }
  }

  playMissSFX() {
    if (this.sfxMuted || !this.ctx) return;
    const now = this.ctx.currentTime;
    const osc = this.ctx.createOscillator();
    const gain = this.ctx.createGain();

    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(110, now);
    osc.frequency.exponentialRampToValueAtTime(40, now + 0.12);

    gain.gain.setValueAtTime(0.12, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

    osc.connect(gain);
    gain.connect(this.ctx.destination);
    osc.start(now);
    osc.stop(now + 0.12);
  }
}
