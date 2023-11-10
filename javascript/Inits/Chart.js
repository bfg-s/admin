window.libs['chart::js'] = function ($parameters) {

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

        $(`#${$parameters.loaderId}`).show();

        axios.get($parameters.load_url + location.search, {params: {name: $parameters.name, _build_modal: 1}})
            .then(d => {
                const data = d.data;

                chart.type = data.type;
                chart.data.labels = data.labels;
                chart.data.datasets = data.datasets;
                chart.options = data.options;
                chart.update();
                $(`#${$parameters.loaderId}`).hide();
            });
    }
};
