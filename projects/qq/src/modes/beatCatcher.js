/**
 * BeatPulse - Mode A: Beat Catcher (Casual / Chill)
 * Character/Target ring sits in the center. Glowing beat orbs stream radially
 * towards center from 4 cardinal directions, synchronized with the uploaded song's beat map.
 */

export class BeatCatcherMode {
  constructor(gameEngine) {
    this.engine = gameEngine;
    this.orbs = [];
    this.targetRadius = 60;
    this.orbSpeed = 4.5;

    // Directions: 0: Up, 1: Right, 2: Down, 3: Left
    this.directionAngles = [
      -Math.PI / 2, // Up
      0,            // Right
      Math.PI / 2,  // Down
      Math.PI       // Left
    ];

    this.directionColors = [
      '#00f0ff', // Up: Cyan
      '#ff007f', // Right: Magenta
      '#ffe600', // Down: Yellow
      '#00ff66'  // Left: Green
    ];

    this.keyMap = {
      'ArrowUp': 0, 'w': 0, 'W': 0,
      'ArrowRight': 1, 'd': 1, 'D': 1,
      'ArrowDown': 2, 's': 2, 'S': 2,
      'ArrowLeft': 3, 'a': 3, 'A': 3
    };
  }

  reset() {
    this.orbs = [];
  }

  spawnOrb(dir = null) {
    if (dir === null) {
      dir = Math.floor(Math.random() * 4);
    }

    const angle = this.directionAngles[dir];
    const spawnDist = Math.max(this.engine.visuals.width, this.engine.visuals.height) * 0.5;

    const startX = this.engine.visuals.centerX + Math.cos(angle) * spawnDist;
    const startY = this.engine.visuals.centerY + Math.sin(angle) * spawnDist;

    this.orbs.push({
      dir,
      x: startX,
      y: startY,
      dist: spawnDist,
      speed: this.orbSpeed + (this.engine.audio ? this.engine.audio.bassEnergy * 2 : 0),
      radius: 16,
      color: this.directionColors[dir],
      angle
    });
  }

  update(deltaTime) {
    const audio = this.engine.audio;

    if (audio) {
      const currentTime = audio.getCurrentTime();
      const spawnDist = Math.max(this.engine.visuals.width, this.engine.visuals.height) * 0.5;
      const travelDistance = spawnDist - this.targetRadius;
      const travelTime = travelDistance / (this.orbSpeed * 60);

      // Check BeatMap lookahead timestamps for uploaded song
      if (audio.beatMap && audio.beatMap.length > 0) {
        while (
          audio.nextBeatIndex < audio.beatMap.length &&
          currentTime + travelTime >= audio.beatMap[audio.nextBeatIndex]
        ) {
          this.spawnOrb();
          audio.nextBeatIndex++;
        }
      } else if (audio.isBeatDetected) {
        // Fallback to real-time adaptive beat detection
        this.spawnOrb();
      }
    }

    // Update orb positions towards center ring
    const centerX = this.engine.visuals.centerX;
    const centerY = this.engine.visuals.centerY;

    for (let i = this.orbs.length - 1; i >= 0; i--) {
      const orb = this.orbs[i];
      orb.dist -= orb.speed * (deltaTime * 60);

      orb.x = centerX + Math.cos(orb.angle) * orb.dist;
      orb.y = centerY + Math.sin(orb.angle) * orb.dist;

      // Missed orb passed through central target
      if (orb.dist < -30) {
        this.engine.registerMiss(orb.x, orb.y);
        this.orbs.splice(i, 1);
      }
    }
  }

  handleKeyDown(key) {
    if (this.keyMap[key] === undefined) return;
    const pressedDir = this.keyMap[key];

    // Find nearest orb matching direction
    let closestOrb = null;
    let closestIndex = -1;
    let minDistanceToRing = Infinity;

    for (let i = 0; i < this.orbs.length; i++) {
      const orb = this.orbs[i];
      if (orb.dir === pressedDir) {
        const diff = Math.abs(orb.dist - this.targetRadius);
        if (diff < minDistanceToRing) {
          minDistanceToRing = diff;
          closestOrb = orb;
          closestIndex = i;
        }
      }
    }

    if (closestOrb && minDistanceToRing < 65) {
      let rating = 'GOOD';
      let score = 50;

      if (minDistanceToRing < 18) {
        rating = 'PERFECT';
        score = 300;
      } else if (minDistanceToRing < 38) {
        rating = 'GREAT';
        score = 150;
      }

      this.engine.registerHit(closestOrb.x, closestOrb.y, score, rating, closestOrb.color);
      this.orbs.splice(closestIndex, 1);
    } else {
      // Pressed wrong direction or no orb nearby
      this.engine.registerMiss(
        this.engine.visuals.centerX + Math.cos(this.directionAngles[pressedDir]) * this.targetRadius,
        this.engine.visuals.centerY + Math.sin(this.directionAngles[pressedDir]) * this.targetRadius
      );
    }
  }

  render(ctx, visuals) {
    const cx = visuals.centerX;
    const cy = visuals.centerY;

    // Draw central target ring
    const bass = this.engine.audio ? this.engine.audio.bassEnergy : 0.2;
    const currentRingRadius = this.targetRadius + (bass * 12);

    ctx.save();
    ctx.strokeStyle = '#00f0ff';
    ctx.lineWidth = 4;
    ctx.shadowBlur = 20;
    ctx.shadowColor = '#00f0ff';
    ctx.beginPath();
    ctx.arc(cx, cy, currentRingRadius, 0, Math.PI * 2);
    ctx.stroke();

    // Directional indicator arrows
    const dirs = ['▲', '►', '▼', '◄'];
    ctx.font = '700 18px Orbitron';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    for (let i = 0; i < 4; i++) {
      const angle = this.directionAngles[i];
      const tx = cx + Math.cos(angle) * (currentRingRadius + 22);
      const ty = cy + Math.sin(angle) * (currentRingRadius + 22);
      ctx.fillStyle = this.directionColors[i];
      ctx.fillText(dirs[i], tx, ty);
    }
    ctx.restore();

    // Draw incoming orbs
    for (const orb of this.orbs) {
      ctx.save();
      ctx.fillStyle = orb.color;
      ctx.shadowBlur = 18;
      ctx.shadowColor = orb.color;

      ctx.beginPath();
      ctx.arc(orb.x, orb.y, orb.radius, 0, Math.PI * 2);
      ctx.fill();

      // Outer glow ring
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.arc(orb.x, orb.y, orb.radius + 4, 0, Math.PI * 2);
      ctx.stroke();
      ctx.restore();
    }
  }
}
