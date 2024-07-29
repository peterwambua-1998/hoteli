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
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        padding: 10px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
    }
    .key {
        width:90%;
        height: 100px;
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
    <div class="key backspace">←</div>
</div>

<script>
    const keys = document.querySelectorAll('.key');
    const inputField1 = document.getElementById('userEmail');
    const inputField2 = document.getElementById('myInput');
    let activeInputField = null;

    console.log(inputField2);


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
            } else if (key.classList.contains('space')) {
                    inputField.value += ' ';
            }else {
                activeInputField.value += key.textContent;
            }
            triggerInputEvent(activeInputField);
        });
    });

    

    inputField2.addEventListener('input', () => {
        console.log('Input 2 changed:', inputField2.value);
    });

    // Initially set the first input field as active
    setActiveInputField(inputField2);
</script>