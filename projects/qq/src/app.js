/**
 * BeatPulse - Main App Entry
 * Connects Audio Engine, Visual Engine, Game Engine, and UI elements.
 * Features Clean Aesthetic UI, Lo-Fi Preset Library, Full 00:00 MP3 Audio Upload, and Mobile HP Touch Controls.
 */

import { AudioEngine } from './audio.js';
import { VisualEngine } from './visuals.js';
import { GameEngine } from './gameEngine.js';

document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('game-canvas');
  const audioEngine = new AudioEngine();
  const visualEngine = new VisualEngine(canvas);
  const gameEngine = new GameEngine(canvas, audioEngine, visualEngine);

  // UI DOM Elements
  const menuScreen = document.getElementById('menu-screen');
  const pauseScreen = document.getElementById('pause-screen');
  const gameOverScreen = document.getElementById('gameover-screen');
  const hudOverlay = document.getElementById('hud-overlay');

  const startBtn = document.getElementById('start-btn');
  const resumeBtn = document.getElementById('resume-btn');
  const quitBtn = document.getElementById('quit-btn');
  const replayBtn = document.getElementById('replay-btn');
  const menuBtn = document.getElementById('menu-btn');
  const sfxToggleBtn = document.getElementById('sfx-toggle-btn');

  // Search Elements
  const songSearchInput = document.getElementById('song-search-input');
  const songSearchBtn = document.getElementById('song-search-btn');
  const searchResultsList = document.getElementById('search-results-list');

  const modeCards = document.querySelectorAll('.mode-card');
  const songChips = document.querySelectorAll('.song-chip');
  const fileInput = document.getElementById('audio-file-input');
  const dropZone = document.getElementById('drop-zone');

  const hudScore = document.getElementById('hud-score');
  const hudCombo = document.getElementById('hud-combo');
  const hudMultiplier = document.getElementById('hud-multiplier');
  const hudProgressBar = document.getElementById('hud-progress-bar');
  const hudTrackTitle = document.getElementById('hud-track-title');
  const laneKeysOverlay = document.getElementById('lane-keys-overlay');
  const keyBadges = laneKeysOverlay ? laneKeysOverlay.querySelectorAll('.key-badge') : [];

  // Mobile Touch Controls DOM
  const mobileRunnerTouchpad = document.getElementById('mobile-runner-touchpad');
  const mobileCatcherDpad = document.getElementById('mobile-catcher-dpad');

  // Initial Default Built-in Lofi Synth Track (Zero Alert on Load)
  audioEngine.loadSynthPreset('lofi');
  hudTrackTitle.textContent = audioEngine.trackName;

  // SFX Toggle
  if (sfxToggleBtn) {
    sfxToggleBtn.addEventListener('click', () => {
      audioEngine.sfxMuted = !audioEngine.sfxMuted;
      sfxToggleBtn.textContent = audioEngine.sfxMuted
        ? '🔇 Hit SFX: OFF (Pure Music)'
        : '🔊 Hit Percussion SFX: ON';
    });
  }

  // Preset Chips Handler
  songChips.forEach((chip) => {
    chip.addEventListener('click', () => {
      songChips.forEach((c) => c.classList.remove('active'));
      chip.classList.add('active');

      const presetKey = chip.getAttribute('data-preset');
      if (presetKey) {
        audioEngine.loadSynthPreset(presetKey);
        hudTrackTitle.textContent = audioEngine.trackName;
        if (searchResultsList) searchResultsList.innerHTML = '';
      }
    });
  });

  // Online Search Handler
  const executeSearch = async (query) => {
    if (!query || !query.trim()) return;
    searchResultsList.innerHTML = `<div style="grid-column: 1/-1; padding: 12px; color: var(--accent-teal);">🔍 Searching "${query}"...</div>`;

    try {
      const jamendoUrl = `https://api.jamendo.com/v3.0/tracks/?client_id=56d30c4f&format=jsonpretty&limit=8&namesearch=${encodeURIComponent(query)}&audioformat=mp32`;
      const res = await fetch(jamendoUrl);
      const data = await res.json();

      if (!data.results || data.results.length === 0) {
        searchResultsList.innerHTML = `<div style="grid-column: 1/-1; padding: 12px; color: var(--text-muted);">Tidak ditemukan lagu untuk "${query}". Coba kata kunci lain.</div>`;
        return;
      }

      searchResultsList.innerHTML = '';
      data.results.forEach((track) => {
        const card = document.createElement('div');
        card.className = 'search-track-card';
        card.innerHTML = `
          <img class="track-artwork" src="${track.album_image || 'https://via.placeholder.com/60'}" alt="art" onerror="this.src='https://via.placeholder.com/60'" />
          <div class="track-info-box">
            <div class="track-title-text">${track.name}</div>
            <div class="track-artist-text">${track.artist_name}</div>
          </div>
          <div style="font-size: 1.1rem; color: var(--accent-teal);">▶️ PLAY</div>
        `;

        card.addEventListener('click', async () => {
          document.querySelectorAll('.search-track-card').forEach((c) => c.classList.remove('active'));
          card.classList.add('active');

          const trackTitle = `${track.name} - ${track.artist_name}`;
          const success = await audioEngine.loadAudioFromUrl(track.audio, trackTitle, 180);
          if (success) {
            hudTrackTitle.textContent = trackTitle;
          }
        });

        searchResultsList.appendChild(card);
      });
    } catch (e) {
      searchResultsList.innerHTML = '<div style="grid-column: 1/-1; padding: 12px; color: var(--accent-rose);">Koneksi error. Silakan periksa jaringan internet.</div>';
    }
  };

  if (songSearchBtn && songSearchInput) {
    songSearchBtn.addEventListener('click', () => executeSearch(songSearchInput.value));
    songSearchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') executeSearch(songSearchInput.value);
    });
  }

  // Key Badge Highlight Handler
  const keyIndexMap = {
    'a': 0, 'A': 0, 'ArrowLeft': 0,
    's': 1, 'S': 1, 'ArrowDown': 1,
    'd': 2, 'D': 2, 'ArrowRight': 2
  };

  window.addEventListener('keydown', (e) => {
    if (keyIndexMap[e.key] !== undefined && keyBadges[keyIndexMap[e.key]]) {
      keyBadges[keyIndexMap[e.key]].classList.add('active');
    }
  });

  window.addEventListener('keyup', (e) => {
    if (keyIndexMap[e.key] !== undefined && keyBadges[keyIndexMap[e.key]]) {
      keyBadges[keyIndexMap[e.key]].classList.remove('active');
    }
  });

  // Mobile Touch Controls
  if (mobileRunnerTouchpad) {
    const touchPads = mobileRunnerTouchpad.querySelectorAll('.mobile-touch-pad');
    const laneKeys = ['a', 's', 'd'];

    touchPads.forEach((pad) => {
      const handlePadPress = (e) => {
        e.preventDefault();
        const lane = parseInt(pad.getAttribute('data-lane'), 10);
        if (gameEngine.state === 'PLAYING') {
          gameEngine.activeModeHandler.handleKeyDown(laneKeys[lane]);
          pad.classList.add('active');
          if (keyBadges[lane]) keyBadges[lane].classList.add('active');
        }
      };

      const handlePadRelease = (e) => {
        e.preventDefault();
        const lane = parseInt(pad.getAttribute('data-lane'), 10);
        pad.classList.remove('active');
        if (keyBadges[lane]) keyBadges[lane].classList.remove('active');
        if (gameEngine.activeModeHandler.handleKeyUp) {
          gameEngine.activeModeHandler.handleKeyUp(laneKeys[lane]);
        }
      };

      pad.addEventListener('pointerdown', handlePadPress);
      pad.addEventListener('pointerup', handlePadRelease);
      pad.addEventListener('pointerleave', handlePadRelease);
    });
  }

  if (mobileCatcherDpad) {
    const dpadBtns = mobileCatcherDpad.querySelectorAll('.dpad-btn');

    dpadBtns.forEach((btn) => {
      const handleDpadPress = (e) => {
        e.preventDefault();
        const dirKey = btn.getAttribute('data-dir');
        if (gameEngine.state === 'PLAYING') {
          gameEngine.activeModeHandler.handleKeyDown(dirKey);
          btn.classList.add('active');
        }
      };

      const handleDpadRelease = (e) => {
        e.preventDefault();
        btn.classList.remove('active');
      };

      btn.addEventListener('pointerdown', handleDpadPress);
      btn.addEventListener('pointerup', handleDpadRelease);
      btn.addEventListener('pointerleave', handleDpadRelease);
    });
  }

  // Direct Mobile Canvas Touch Fallback
  canvas.addEventListener('touchstart', (e) => {
    if (gameEngine.state !== 'PLAYING') return;
    if (e.touches.length === 0) return;

    const touch = e.touches[0];
    const rect = canvas.getBoundingClientRect();
    const touchX = touch.clientX - rect.left;

    if (gameEngine.selectedMode === 'RUNNER') {
      const totalWidth = 3 * 110;
      const startX = visualEngine.centerX - (totalWidth / 2);
      if (touchX >= startX && touchX <= startX + totalWidth) {
        const lane = Math.floor((touchX - startX) / 110);
        const keys = ['a', 's', 'd'];
        if (lane >= 0 && lane < 3) {
          gameEngine.activeModeHandler.handleKeyDown(keys[lane]);
        }
      }
    }
  }, { passive: false });

  // Mode Selection Helper
  const updateTouchOverlayVisibility = () => {
    const isTouchDevice = ('ontouchstart' in window || navigator.maxTouchPoints > 0) && window.innerWidth <= 768;

    if (gameEngine.selectedMode === 'RUNNER') {
      laneKeysOverlay.style.display = 'flex';
      if (mobileRunnerTouchpad) mobileRunnerTouchpad.style.display = isTouchDevice ? 'flex' : 'none';
      if (mobileCatcherDpad) mobileCatcherDpad.style.display = 'none';
    } else {
      laneKeysOverlay.style.display = 'none';
      if (mobileRunnerTouchpad) mobileRunnerTouchpad.style.display = 'none';
      if (mobileCatcherDpad) mobileCatcherDpad.style.display = isTouchDevice ? 'block' : 'none';
    }
  };

  // Mode Selection Cards
  modeCards.forEach((card) => {
    card.addEventListener('click', () => {
      modeCards.forEach((c) => c.classList.remove('selected'));
      card.classList.add('selected');

      const selectedMode = card.getAttribute('data-mode');
      gameEngine.setMode(selectedMode);
      updateTouchOverlayVisibility();
    });
  });

  // Local MP3 / Audio File Upload (Full Song Upload)
  const handleFileUpload = async (file) => {
    if (!file) return;
    const arrayBuffer = await file.arrayBuffer();
    const success = await audioEngine.loadAudioBuffer(arrayBuffer, file.name.replace(/\.[^/.]+$/, ''));
    if (success) {
      hudTrackTitle.textContent = audioEngine.trackName;
    }
  };

  fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
      handleFileUpload(e.target.files[0]);
    }
  });

  // Drag and Drop Audio File
  if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
      dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropZone.classList.remove('dragover');
      if (e.dataTransfer.files.length > 0) {
        handleFileUpload(e.dataTransfer.files[0]);
      }
    });
  }

  // Start Game Button
  startBtn.addEventListener('click', () => {
    audioEngine.init();
    menuScreen.classList.add('hidden');
    hudOverlay.classList.remove('hidden');
    updateTouchOverlayVisibility();
    gameEngine.start();
  });

  // Resume Button
  resumeBtn.addEventListener('click', () => {
    pauseScreen.classList.add('hidden');
    gameEngine.togglePause();
  });

  // Quit / Menu Buttons
  quitBtn.addEventListener('click', () => {
    pauseScreen.classList.add('hidden');
    hudOverlay.classList.add('hidden');
    menuScreen.classList.remove('hidden');
    gameEngine.stop();
  });

  menuBtn.addEventListener('click', () => {
    gameOverScreen.classList.add('hidden');
    hudOverlay.classList.add('hidden');
    menuScreen.classList.remove('hidden');
    gameEngine.stop();
  });

  replayBtn.addEventListener('click', () => {
    gameOverScreen.classList.add('hidden');
    gameEngine.start();
  });

  // Game Engine HUD Update callback
  gameEngine.onHUDUpdate = ({ score, combo, multiplier, progress }) => {
    hudScore.textContent = score.toLocaleString();
    hudCombo.textContent = combo;
    hudMultiplier.textContent = `${multiplier}x`;
    hudProgressBar.style.width = `${progress}%`;

    if (gameEngine.state === 'PAUSED') {
      pauseScreen.classList.remove('hidden');
    } else {
      pauseScreen.classList.add('hidden');
    }
  };

  // Game Over Callback
  gameEngine.onGameOver = () => {
    hudOverlay.classList.add('hidden');
    gameOverScreen.classList.remove('hidden');

    document.getElementById('final-score').textContent = gameEngine.score.toLocaleString();
    document.getElementById('final-max-combo').textContent = gameEngine.maxCombo;
    document.getElementById('final-perfect').textContent = gameEngine.perfectHits;
    document.getElementById('final-great').textContent = gameEngine.greatHits;
    document.getElementById('final-good').textContent = gameEngine.goodHits;
    document.getElementById('final-miss').textContent = gameEngine.misses;

    const isNewHigh = gameEngine.saveHighScore();
    const highscoreText = document.getElementById('highscore-notice');
    if (isNewHigh) {
      highscoreText.textContent = '🏆 NEW HIGH SCORE!';
      highscoreText.style.color = '#ffe600';
    } else {
      highscoreText.textContent = `High Score: ${gameEngine.getHighScore().toLocaleString()}`;
      highscoreText.style.color = '#8a99ad';
    }
  };
});
