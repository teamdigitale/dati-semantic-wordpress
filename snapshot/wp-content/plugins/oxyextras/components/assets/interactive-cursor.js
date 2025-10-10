jQuery(document).ready(oxygen_cursor);
function oxygen_cursor($) {
    
        document.querySelectorAll(".oxy-interactive-cursor").forEach((cursor) => {        

                if ( ( 'ontouchstart' in window ) ||  ( navigator.maxTouchPoints > 0 ) || ( navigator.msMaxTouchPoints > 0 ) ) {
                    return;
                }
            
                const cursorTag = cursor
                const balls = cursorTag.querySelectorAll("div")
                const ballMessage = cursorTag.querySelector(".oxy-cursor_ball span")
                const hoverElements = document.querySelectorAll("[data-x-hover]")
                const growElements = document.querySelectorAll( cursorTag.querySelector(".oxy-cursor_ball").dataset.hover )
                const multiplier = cursorTag.querySelector(".oxy-cursor_ball").dataset.speed;
    
                let aimX = 0
                let aimY = 0
    
                balls.forEach((ball, index) => {
                        let currentX = 0
                        let currentY = 0

                        let speed = (multiplier * 2) - (index * multiplier)
                    
                    const animate = function () {
                        currentX += (aimX - currentX) * speed
                        currentY += (aimY - currentY) * speed
    
                        ball.style.left = currentX + "px"
                        ball.style.top = currentY + "px"
    
                        requestAnimationFrame(animate)
                    }
    
                    animate()
                })
    
                document.addEventListener('mousemove', updateCursorPosition, false)
    
                function updateCursorPosition(event) {

                    cursorTag.classList.add("oxy-cursor_ready")
    
                    aimX = event.clientX
                    aimY = event.clientY
    
                    /* shrink when outside document window */
                    if (event.clientY <= 20 || event.clientX <= 20 || (event.clientX >= (window.innerWidth - 20) || event.clientY >= (window.innerHeight - 20))) {  
                        cursorTag.classList.add("oxy-cursor_offpage")
                    } else {
                        cursorTag.classList.remove("oxy-cursor_offpage")
                    }
                }
    
                document.addEventListener("mousedown", function (event) {
                    cursorTag.classList.add("oxy-cursor_mousedown")
                })
    
                document.addEventListener("mouseup", function (event) {
                    cursorTag.classList.remove("oxy-cursor_mousedown")
                })
    
                hoverElements.forEach(hoverElement => {
                    hoverElement.addEventListener("mouseover", function () {
                        cursorTag.classList.add("oxy-cursor_text-visible")
                        cursorTag.classList.add("oxy-cursor_grow")
                        ballMessage.innerHTML = hoverElement.getAttribute("data-x-hover")
                    })
                    
                    hoverElement.addEventListener("mouseout", function () {
                        cursorTag.classList.remove("oxy-cursor_text-visible")
                        cursorTag.classList.remove("oxy-cursor_grow")
                    })
                })

                document.addEventListener("mouseover", growElements)
    
                growElements.forEach(growElement => {
                    growElement.addEventListener("mouseover", function () {
                        cursorTag.classList.add("oxy-cursor_trail-grow")
                    })
                    
                    growElement.addEventListener("mouseout", function () {
                        cursorTag.classList.remove("oxy-cursor_trail-grow")
                    })
                })

         });
       
}