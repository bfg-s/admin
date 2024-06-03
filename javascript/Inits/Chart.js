window.libs['chart::js'] = async function ($parameters) {

    const ctx = this.target;
    const chart = new Chart(ctx, {
        type: $parameters.type,
        data: {
            labels: $parameters.labels,
            datasets: $parameters.datasets
        },
        options: $parameters.options,
    });

    if ($parameters.loading) {

        const call = (loading = true) => {

            if (loading) {
                $(`#${$parameters.loaderId}`).show();
            }
            const token = exec('token');
            axios.post(window.load_chart_js + location.search, {name: $parameters.name, _build_modal: 1, _token: token})
                .then(d => {
                    const data = d.data;

                    // Сохранение текущего состояния видимости
                    const visibility = saveDatasetVisibility(chart);

                    chart.type = data.type;
                    chart.data.labels = data.labels;
                    chart.data.datasets = data.datasets;
                    chart.options = Array.isArray(data.options) ? {} : data.options;
                    if (! loading) {
                        chart.options.animation = { duration: 0 };
                    }
                    restoreDatasetVisibility(chart, visibility);
                    chart.update();
                    if (loading) {
                        $(`#${$parameters.loaderId}`).hide();
                    }
                });

        };

        const intervalKey = setInterval(() => call(false), $parameters.timeout);

        call();

        $(document).on('pjax:complete', () => {

            clearInterval(intervalKey);
        });
    }
};

// Функция для сохранения состояния видимости каждого набора данных
function saveDatasetVisibility(chart) {
    const visibility = {};
    chart.data.datasets.forEach((dataset, index) => {
        visibility[index] = !chart.isDatasetVisible(index);
    });
    return visibility;
}

// Функция для восстановления состояния видимости каждого набора данных
function restoreDatasetVisibility(chart, visibility) {
    chart.data.datasets.forEach((dataset, index) => {
        chart.getDatasetMeta(index).hidden = visibility[index];
    });
}
