document.addEventListener('DOMContentLoaded', function () {
    // === Обробка слайдера ===
    const slider = document.getElementById('mood-slider');
    const moodTextInput = document.getElementById('mood-text');

    function convertSliderToMood(value) {
        if (value <= 25) return "Дуже погано";
        if (value <= 40) return "Погано";
        if (value <= 60) return "Нейтрально";
        if (value <= 75) return "Добре";
        return "Чудово";
    }

    function updateMood() {
        if (slider && moodTextInput) {
            moodTextInput.value = convertSliderToMood(slider.value);
        }
    }

    if (slider) {
        slider.addEventListener('input', updateMood);
        updateMood(); // встановити одразу при завантаженні
    }

    // === Побудова графіка ===
    const moodLabels = window.moodChartLabels || [];
    const moodRawData = window.moodChartData || [];

    const moodToValue = {
        "Дуже погано": 20,
        "Погано": 35,
        "Нейтрально": 50,
        "Добре": 75,
        "Чудово": 100
    };

    const numericMoodData = moodRawData.map(mood => moodToValue[mood] || 0);

    const ctx = document.getElementById('moodChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: moodLabels,
                datasets: [{
                    label: 'Настрій',
                    data: numericMoodData,
                    borderColor: '#759175',
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Рівень настрою'
                        }
                    }
                }
            }
        });
    }
});