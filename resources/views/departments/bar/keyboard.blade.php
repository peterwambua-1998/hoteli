<style>
    
    .keyboard {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        padding: 10px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
    <div class="key">Q</div>
    <div class="key">W</div>
    <div class="key">E</div>
    <div class="key">R</div>
    <div class="key">T</div>
    <div class="key">Y</div>
    <div class="key">U</div>
    <div class="key">I</div>
    <div class="key">O</div>
    <div class="key">P</div>
    <div class="key">A</div>
    <div class="key">S</div>
    <div class="key">D</div>
    <div class="key">F</div>
    <div class="key">G</div>
    <div class="key">H</div>
    <div class="key">J</div>
    <div class="key">K</div>
    <div class="key">L</div>
    <div class="key">Z</div>
    <div class="key">X</div>
    <div class="key">C</div>
    <div class="key">V</div>
    <div class="key">B</div>
    <div class="key">N</div>
    <div class="key">M</div>
    
    <div class="key backspace">←</div>
    <div class="key space">Space</div>

</div>