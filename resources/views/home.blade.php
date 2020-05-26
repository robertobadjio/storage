<html>
<head></head>
<body>
<script>
    (function () {
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style.fontFamily = "monospace";
        a.style.textAlign = "center";
        a.style.fontSize = "100px";
        a.style.display = "block";
        a.style.transform = "rotate(" + parseInt(Math.random() * 45 * (Math.random() > 0.5 ? -1 : 1)) + "deg)";
        a.href = 'https://robertobadjio.ru';
        a.style.textDecoration = "none";
        a.style.color = 'black';

        (function (ms) {
            this.anim = [".(^E^)'", "-(^l^)-", "'(^j^).", "-(^u^)-", ".(^r^)'", "-(^-^)-", "'(^-^).", "-(^-^)-", ".(^-^)'", "-(^-^)-", "'(^-^).", "-(^o^)-", ".(^-^)'", "-(^-^)-", "'(^-^).", "-(^-^)-"];
            var anim = this.anim;
            var currentFrame = 0;
            setInterval(function () {
                a.innerText = "\n\n" + anim[currentFrame];
                currentFrame++;
                if (currentFrame >= anim.length) currentFrame = 0;
            }, ms);
        })(100)
    })();
</script>
</body>
</html>