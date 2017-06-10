import Task from './task'
import ko from 'knockout'

const tab = "  ";

class EstimateListViewModel
{
    constructor()
    {
        this.tasks = ko.observableArray([]);

        this.estimate = {
            newItemTitle: ko.observable(''),
            newItemEstimate: ko.observable('')
        };

        this.projectName = ko.observable('');
        this.taskDescription = ko.observable('');

        this.editorMode = ko.observable('tableView');

        this.textTasks = ko.observable('');

        this.fileToUpload = ko.observable('');

        this.removeTask = this.removeTask.bind(this);
        this.removeSubTask = this.removeSubTask.bind(this);

        this.totalEstimate = ko.computed(() => {
            let total = 0;

            this.tasks().map((task) => {
                total += (task.subTasks().length === 0 ? parseFloat(task.estimate().toString().replace(',','.')) : 0) + parseFloat(task.subTasksTotal())
            });

            total = total ? total : 0;

            return total
        });
    }

    addTask()
    {
        if (!this.estimate.newItemTitle())
        {
            return
        }

        this.tasks.push(new Task({ title: this.estimate.newItemTitle(), estimate: this.estimate.newItemEstimate(), subTasks: [], subEstimate: { newItemTitle: '', newItemEstimate: '' } }));

        this.estimate.newItemTitle('');
        this.estimate.newItemEstimate('');
    }

    addSubTask(index, task)
    {
        if (!task.subEstimate.newItemTitle() && !task.subEstimate.newItemEstimate())
        {
            return
        }

        this.tasks()[index].subTasks.push({ title: ko.observable(task.subEstimate.newItemTitle()), estimate: ko.observable(task.subEstimate.newItemEstimate()) });

        task.subEstimate.newItemTitle('');
        task.subEstimate.newItemEstimate('');
    }

    removeTask(task)
    {
        this.tasks.remove(task)
    }

    removeSubTask(index, task)
    {
        this.tasks()[index].subTasks.remove(task)
    }

    handleTaskKeypress(d, e)
    {
        if (e.keyCode !== 13)
        {
            return true
        }

        this.addTask()
    }

    handleSubTaskKeypress(index, task, e)
    {
        if (e.keyCode !== 13)
        {
            return true
        }

        this.addSubTask(index, task)
    }

    loadJson(e)
    {
        const file = e.target.files[0];

        const reader = new FileReader();

        reader.onload = (file) => {
            const data = JSON.parse(file.target.result);

            this.tasks(data.tasks.map((task) => new Task(task)));

            if (this.editorMode() === 'textView') {
                this.convertToTextFormat()
            }

            this.projectName(data.projectName);

            this.taskDescription(data.taskDescription)
        };

        reader.readAsText(file);

        this.fileToUpload('')
    }

    convertToTextFormat()
    {
        let text = '';

        this.tasks().map((task) => {
            if (text) {
                text += "\n"
            }

            text += task.title();

            if (task.subTasks().length) {
                task.subTasks().map((subTask) => {
                    text += "\n" + `${tab}${subTask.title()} - ${subTask.estimate().toString()}`
                })
            } else {
                text += ` - ${task.finalEstimate().toString()}`
            }
        });

        this.textTasks(text)
    }

    convertToTableFormat()
    {
        if (this.textTasks().length === 0) {
            this.tasks([]);

            return
        }

        const tasks = this.textTasks().split("\n");

        let resultTasks = [];

        let mainTask = null;

        tasks.map((taskAndEstimate) => {
            if (taskAndEstimate.indexOf(tab) === -1) {
                if (mainTask) {                    
                    resultTasks.push(mainTask)
                }

                const task = taskAndEstimate.split(" - ");

                mainTask = new Task({ title: task[0], estimate: task.length === 2 ? task[1] : 0, subTasks: [], subEstimate: { newItemTitle: '', newItemEstimate: '' } });
            } else {
                const subTask = taskAndEstimate.replace(tab, "").split(" - ");

                mainTask.subTasks.push({ title: ko.observable(subTask[0]), estimate: ko.observable(subTask[1]) });
            }
        });

        if (mainTask) {                    
            resultTasks.push(mainTask)
        }

        this.tasks(resultTasks)
    }

    toggleEditorMode()
    {
        this.editorMode(this.editorMode() === 'tableView' ? 'textView' : 'tableView');

        if (this.editorMode() === 'textView') {
            this.convertToTextFormat();
        } else {
            this.convertToTableFormat();
        }
    }
}

export default EstimateListViewModel