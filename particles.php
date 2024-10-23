<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      * {
        box-sizing: border-box;
      }
      html, body {
        height: 100%;
        margin: 0;
      }
      body {
        position: relative; 
      }
      canvas {
        position: fixed;
        top: 0;
        left: 0;
        pointer-events: none; 
        z-index: 9999;
      }
    </style>
</head>
<body>
    <!-- Canvas для частиц -->
    <canvas id="particleCanvas"></canvas>

    <script>
      class PointerParticle {
        constructor(spread, speed, component) {
          const { ctx, pointer } = component;
      
          this.ctx = ctx;
          this.x = pointer.x;
          this.y = pointer.y;
          this.mx = pointer.mx * 0.1;
          this.my = pointer.my * 0.1;
          this.size = Math.random() * 3 + 1;  
          this.decay = 0.05;  
          this.speed = speed * 0.08;
          this.spread = spread * this.speed;
          this.spreadX = (Math.random() - 0.5) * this.spread - this.mx;
          this.spreadY = (Math.random() - 0.5) * this.spread - this.my;
          this.color = 'white';  
        }
      
        draw() {
          this.ctx.fillStyle = this.color;
          this.ctx.fillRect(this.x, this.y, this.size, this.size);
        }
      
        collapse() {
          this.size -= this.decay;
        }
      
        trail() {
          this.x += this.spreadX * this.size;
          this.y += this.spreadY * this.size;
        }
      
        update() {
          this.draw();
          this.trail();
          this.collapse();
        }
      }
      
      class PointerParticles {
        constructor() {
          this.canvas = document.getElementById('particleCanvas');
          this.ctx = this.canvas.getContext('2d');
          this.fps = 60;
          this.msPerFrame = 1000 / this.fps;
          this.timePrevious = performance.now();
          this.particles = [];
          this.pointer = {
            x: 0,
            y: 0,
            mx: 0,
            my: 0
          };
          
          this.setup();
        }
      
        setup() {
          this.setCanvasDimensions();
          this.setupEvents();
          this.animateParticles();
        }
      
        createParticles(event, { count, speed, spread }) {
          this.setPointerValues(event);

          const particlesCount = {
            click: 13, 
            move: 1  
          };

          let currentCount = count;
          if (event.type === 'click') {
            currentCount = particlesCount.click;
          } else if (event.type === 'pointermove') {
            currentCount = particlesCount.move;
          }

          for (let i = 0; i < currentCount; i++) {
            this.particles.push(new PointerParticle(spread, speed, this));
          }
        }
      
        setPointerValues(event) {
          this.pointer.x = event.clientX; 
          this.pointer.y = event.clientY;
          this.pointer.mx = event.movementX;
          this.pointer.my = event.movementY;
        }
      
        setupEvents() {
          document.addEventListener("click", (event) => {
            this.createParticles(event, {
              count: 30, 
              speed: Math.random() + 1,
              spread: Math.random() + 30
            });
          });
      
          document.addEventListener("pointermove", (event) => {
            this.createParticles(event, {
              count: 5, 
              speed: this.getSpeed(event),
              spread: 1
            });
          });
      
          window.addEventListener("resize", () => this.setCanvasDimensions());
        }
      
        getSpeed(event) {
          const a = event.movementX;
          const b = event.movementY;
          return Math.floor(Math.sqrt(a * a + b * b));
        }
      
        handleParticles() {
          for (let i = 0; i < this.particles.length; i++) {
            this.particles[i].update();
      
            if (this.particles[i].size <= 0.05) {
              this.particles.splice(i, 1);
              i--;
            }
          }
        }
      
        setCanvasDimensions() {
          this.canvas.width = window.innerWidth;
          this.canvas.height = window.innerHeight;
        }
      
        animateParticles() {
          requestAnimationFrame(() => this.animateParticles());
      
          const timeNow = performance.now();
          const timePassed = timeNow - this.timePrevious;
      
          if (timePassed < this.msPerFrame) return;
      
          const excessTime = timePassed % this.msPerFrame;
      
          this.timePrevious = timeNow - excessTime;
      
          this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      
          this.handleParticles();
        }
      }
      
      new PointerParticles();
    </script>
</body>
</html>
