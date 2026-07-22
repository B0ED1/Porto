/**
 * BeatPulse - Mode B: Neon Rhythm Runner (Arcade)
 * 3 Vertical lanes with descending glowing tiles, synchronized with the uploaded song's beat map.
 * Key Controls:
 * Lane 0 (Left):   'A' or 'ArrowLeft'
 * Lane 1 (Center): 'S' or 'ArrowDown'
 * Lane 2 (Right):  'D' or 'ArrowRight'
 */

export class RhythmRunnerMode {
  constructor(gameEngine) {
    this.engine = gameEngine;
    this.tiles = [];
    this.laneCount = 3;
    this.laneWidth = 110;
    this.hitLineYRatio = 0.72; // Raised target line to 72% height for 100% clean visibility
    this.tileSpeed = 8.5;

    this.laneColors = ['#2dd4bf', '#c084fc', '#fbbf24'];
    this.laneLabels = ['A', 'S', 'D'];

    this.keyMap = {
      'a': 0, 'A': 0, 'ArrowLeft': 0,
      's': 1, 'S': 1, 'ArrowDown': 1,
      'd': 2, 'D': 2, 'ArrowRight': 2
    };

    this.lanePressedState = [false, false, false];
  }

  reset() {
    this.tiles = [];
    this.lanePressedState = [false, false, false];
  }

  spawnTile(lane = null) {
    if (lane === null) {
      lane = Math.floor(Math.random() * this.laneCount);
    }

    this.tiles.push({
      lane,
      y: -60,
      height: 48,
      color: this.laneColors[lane]
    });
  }

  update(deltaTime) {
    const audio = this.engine.audio;

    if (audio) {
      const currentTime = audio.getCurrentTime();
      const hitY = this.engine.visuals.height * this.hitLineYRatio;
      const travelDistance = hitY - (-60);
      const travelTime = travelDistance / (this.tileSpeed * 60);

      // Check BeatMap lookahead timestamps for uploaded song
      if (audio.beatMap && audio.beatMap.length > 0) {
        while (
          audio.nextBeatIndex < audio.beatMap.length &&
          currentTime + travelTime >= audio.beatMap[audio.nextBeatIndex]
        ) {
          this.spawnTile();
          audio.nextBeatIndex++;
        }
      } else if (audio.isBeatDetected) {
        // Fallback to real-time adaptive beat detection
        this.spawnTile();
      }
    }

    const hitY = this.engine.visuals.height * this.hitLineYRatio;

    // Move tiles down
    for (let i = this.tiles.length - 1; i >= 0; i--) {
      const tile = this.tiles[i];
      tile.y += this.tileSpeed * (deltaTime * 60);

      // Passed hit line without tap
      if (tile.y > hitY + 50) {
        const laneX = this.getLaneX(tile.lane);
        this.engine.registerMiss(laneX, hitY);
        this.tiles.splice(i, 1);
      }
    }
  }

  getLaneX(lane) {
    const totalWidth = this.laneCount * this.laneWidth;
    const startX = this.engine.visuals.centerX - (totalWidth / 2);
    return startX + (lane * this.laneWidth) + (this.laneWidth / 2);
  }

  handleKeyDown(key) {
    if (this.keyMap[key] === undefined) return;
    const lane = this.keyMap[key];
    this.lanePressedState[lane] = true;

    const hitY = this.engine.visuals.height * this.hitLineYRatio;
    const laneX = this.getLaneX(lane);

    // Find closest tile in pressed lane
    let closestTile = null;
    let closestIndex = -1;
    let minDistance = Infinity;

    for (let i = 0; i < this.tiles.length; i++) {
      const tile = this.tiles[i];
      if (tile.lane === lane) {
        const dist = Math.abs(tile.y - hitY);
        if (dist < minDistance) {
          minDistance = dist;
          closestTile = tile;
          closestIndex = i;
        }
      }
    }

    if (closestTile && minDistance < 70) {
      let rating = 'GOOD';
      let score = 50;

      if (minDistance < 18) {
        rating = 'PERFECT';
        score = 300;
      } else if (minDistance < 38) {
        rating = 'GREAT';
        score = 150;
      }

      this.engine.registerHit(laneX, hitY, score, rating, closestTile.color);
      this.tiles.splice(closestIndex, 1);
    } else {
      // Miss timing or empty tap
      this.engine.registerMiss(laneX, hitY);
    }
  }

  handleKeyUp(key) {
    if (this.keyMap[key] !== undefined) {
      const lane = this.keyMap[key];
      this.lanePressedState[lane] = false;
    }
  }

  render(ctx, visuals) {
    const totalWidth = this.laneCount * this.laneWidth;
    const startX = visuals.centerX - (totalWidth / 2);
    const hitY = visuals.height * this.hitLineYRatio;

    // Draw perspective highway lanes
    ctx.save();
    for (let i = 0; i < this.laneCount; i++) {
      const x = startX + (i * this.laneWidth);

      // Lane background fill when key pressed
      const isPressed = this.lanePressedState[i];
      ctx.fillStyle = isPressed
        ? `rgba(${i === 0 ? '45, 212, 191' : i === 1 ? '192, 132, 252' : '251, 191, 36'}, 0.22)`
        : 'rgba(255, 255, 255, 0.02)';
      ctx.fillRect(x, 0, this.laneWidth, visuals.height);

      // Lane boundary vertical grid lines
      ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x, visuals.height);
      ctx.stroke();

      if (i === this.laneCount - 1) {
        ctx.beginPath();
        ctx.moveTo(x + this.laneWidth, 0);
        ctx.lineTo(x + this.laneWidth, visuals.height);
        ctx.stroke();
      }
    }

    // Draw Target Hit Bar Line (100% clean & unobstructed at 72% height)
    ctx.strokeStyle = '#ffffff';
    ctx.lineWidth = 4;
    ctx.shadowBlur = 18;
    ctx.shadowColor = '#2dd4bf';
    ctx.beginPath();
    ctx.moveTo(startX - 15, hitY);
    ctx.lineTo(startX + totalWidth + 15, hitY);
    ctx.stroke();

    // Target receptors at the hit bar
    for (let i = 0; i < this.laneCount; i++) {
      const rx = startX + (i * this.laneWidth) + (this.laneWidth / 2);
      ctx.strokeStyle = this.lanePressedState[i] ? '#ffffff' : this.laneColors[i];
      ctx.lineWidth = this.lanePressedState[i] ? 4 : 2.5;
      ctx.shadowBlur = 14;
      ctx.shadowColor = this.laneColors[i];
      ctx.beginPath();
      ctx.roundRect(rx - (this.laneWidth / 2) + 8, hitY - 24, this.laneWidth - 16, 48, 10);
      ctx.stroke();
    }
    ctx.restore();

    // Draw Falling Rhythm Tiles
    for (const tile of this.tiles) {
      const x = startX + (tile.lane * this.laneWidth) + 8;
      const width = this.laneWidth - 16;

      ctx.save();
      ctx.fillStyle = tile.color;
      ctx.shadowBlur = 18;
      ctx.shadowColor = tile.color;

      ctx.beginPath();
      ctx.roundRect(x, tile.y - (tile.height / 2), width, tile.height, 10);
      ctx.fill();

      // Top highlight line on tile
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(x + 6, tile.y - (tile.height / 2) + 3, width - 12, 4);
      ctx.restore();
    }
  }
}
