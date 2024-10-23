<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #starField {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; 
            overflow: hidden;
        }

        .star {
            position: absolute;
            border-radius: 50%;
            background: white;
            opacity: 0; 
            animation: twinkle 2s infinite, fall linear infinite; 
        }

        @keyframes twinkle {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 0.6; 
            }
            100% {
                opacity: 0;
            }
        }

        @keyframes fall {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(100vh); 
            }
        }
    </style>
</head>
<body>
    <div id="starField"></div>

    <script>
        function createStars() {
            const starField = document.getElementById('starField');
            const numberOfStars = 300; // Количество точек ебаных

            for (let i = 0; i < numberOfStars; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                const size = Math.random() * 4 + 1;
                const x = Math.random() * window.innerWidth; 
                const y = Math.random() * window.innerHeight;
                const animationDuration = Math.random() * 3 + 3;
                const fallDuration = Math.random() * 50 + 30;

                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.top = `${y}px`;
                star.style.left = `${x}px`;
                star.style.animationDuration = `${animationDuration}s, ${fallDuration}s`; 

                starField.appendChild(star);
            }
        }

        window.onload = createStars;
    </script>
</body>
</html>
