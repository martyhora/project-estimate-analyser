import ko from 'knockout'

class Task
{
    constructor(task)
    {
        this.title = ko.observable(task.title)
        this.estimate = ko.observable(task.estimate)
        this.subTasks = ko.observableArray(task.subTasks.map((subTask) => {
            return { title: ko.observable(subTask.title), estimate: ko.observable(subTask.estimate) }
        }))

        this.subEstimate = { newItemTitle: ko.observable(task.subEstimate.newItemTitle), newItemEstimate: ko.observable(task.subEstimate.newItemEstimate) },

        this.subTasksTotal = ko.computed(() => { 
            let total = 0

            this.subTasks().map((subTask) => {
                total += parseFloat(subTask.estimate().replace(',','.'))
            })

            return total ? total : 0
        })

        this.finalEstimate = ko.computed(() => this.subTasks().length == 0 ? this.estimate() : this.subTasksTotal())
    }
}

export default Task