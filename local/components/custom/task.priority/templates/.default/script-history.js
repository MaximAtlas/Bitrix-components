BX.ready(function() {
    Vue.createApp({
        data() {
            return {
                history: historyData,
            };
        },
        methods: {
/*            loadHistory() {
                fetch('/task_priority/history')
                    .then(response => response.json())
                    .then(data => {
                        this.history = data;
                    })
                    .catch(error => {
                        console.error('Ошибка запроса:', error);
                    });
            }*/

        },
/*        mounted() {
            this.loadHistory();
        }*/
    }).mount('#history');
});
