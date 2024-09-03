BX.ready(function() {
    Vue.createApp({
        data() {
            return {
                tasks: tasksData,
            };
        },
        methods: {
            changePriority(taskId, action) {
                const formData = new FormData();
                formData.append('task_id', taskId);
                formData.append('action', action);

                fetch('/task_priority/update', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Parsed data:', data);
                        if (!data.success) {
                            console.error('Error changing priority:', data.error);
                            return;
                        }

                        const task = this.tasks.find(t => t.ID === taskId);
                        if (!task) {
                            console.error('Task not found');
                            return;
                        }

                        let priority = parseInt(task.UF_PRIORITY, 10);
                        if (action === 'increase') {
                            priority += 1;
                        } else if (action === 'decrease') {
                            priority -= 1;
                        }

                        task.UF_PRIORITY = priority;

                        this.sortTasks();
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                    });
            },
            sortTasks() {
                setTimeout(() => {
                    this.tasks.sort((a, b) => a.UF_PRIORITY - b.UF_PRIORITY);
                }, 3000);
            },
        },
    }).mount('#task');
});

