<style>
    /* body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f4f4f9;
        margin: 0;
        font-family: Arial, sans-serif;
    } */
    .keyboard {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        padding: 10px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
    }
    .key {
        width: 70px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #e3e6ea;
        border: 1px solid #d1d5da;
        border-radius: 5px;
        box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.2);
        font-size: 18px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        transition: background-color 0.2s, box-shadow 0.2s;
    }
    .key:hover {
        background-color: #d1d5da;
        box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.3);
    }
    .key:active {
        background-color: #c0c4c8;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.4);
    }
</style>

<div class="keyboard">
    <div class="key">a</div>
    <div class="key">b</div>
    <div class="key">c</div>
    <div class="key">d</div>
    <div class="key">e</div>
    <div class="key">f</div>
    <div class="key">g</div>
    <div class="key">h</div>
    <div class="key">i</div>
    <div class="key">j</div>
    <div class="key">k</div>
    <div class="key">l</div>
    <div class="key">m</div>
    <div class="key">n</div>
    <div class="key">o</div>
    <div class="key">P</div>
    <div class="key">q</div>
    <div class="key">r</div>
    <div class="key">s</div>
    <div class="key">t</div>
    <div class="key">u</div>
    <div class="key">v</div>
    <div class="key">w</div>
    <div class="key">x</div>
    <div class="key">y</div>
    <div class="key">z</div>
    <div class="key">0</div>
    <div class="key">1</div>
    <div class="key">2</div>
    <div class="key">3</div>
    <div class="key">4</div>
    <div class="key">5</div>
    <div class="key">6</div>
    <div class="key">7</div>
    <div class="key">8</div>
    <div class="key">9</div>
    <div class="key">@</div>
    <div class="key">.</div>
    <div class="key backspace">←</div>
</div>

<script>
    const keys = document.querySelectorAll('.key');
    const inputField1 = document.getElementById('userEmail');
    const inputField2 = document.getElementById('myInput');
    let activeInputField = null;

    inputField1.addEventListener('focus', () => {
        setActiveInputField(inputField1);
    });

    inputField2.addEventListener('focus', () => {
        setActiveInputField(inputField2);
    });

    function setActiveInputField(inputField) {
        if (activeInputField) {
            activeInputField.classList.remove('input-field-active');
        }
        activeInputField = inputField;
        activeInputField.classList.add('input-field-active');
    }

    function triggerInputEvent(element) {
        const event = new Event('input', {
            bubbles: true,
            cancelable: true,
        });
        element.dispatchEvent(event);
    }

    keys.forEach(key => {
        key.addEventListener('click', () => {
            if (!activeInputField) return;
            if (key.textContent === '←') {
                activeInputField.value = activeInputField.value.slice(0, -1);
            } else {
                activeInputField.value += key.textContent;
            }
            triggerInputEvent(activeInputField);
        });
    });

    inputField1.addEventListener('input', () => {
        console.log('Input 1 changed:', inputField1.value);
    });

    inputField2.addEventListener('input', () => {
        console.log('Input 2 changed:', inputField2.value);
    });

    // Initially set the first input field as active
    setActiveInputField(inputField1);
</script>