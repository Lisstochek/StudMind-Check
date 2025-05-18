document.addEventListener("DOMContentLoaded", function() {
    const moodSlider = document.getElementById("mood-slider");
    const moodValue = document.getElementById("mood-value");
    
    moodSlider.addEventListener("input", function() {
        moodValue.textContent = moodSlider.value;
    });

    const recordButton = document.querySelector(".record-btn");
    recordButton.addEventListener("click", function() {
        const selectedMood = moodSlider.value;
        const selectedFactors = Array.from(document.querySelectorAll(".checkbox-container input:checked"))
                                  .map(checkbox => checkbox.value);
        
        console.log("Записаний стан:", selectedMood);
        console.log("Фактори впливу:", selectedFactors);
        
        alert("Ваш стан записано!");
    });
});
