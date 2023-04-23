import React from "react";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, router, useForm} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import InputError from "@/Components/InputError";

function Project({auth, project, tasks}) {
    const {data, setData, errors, post, processing, reset, transform} = useForm({title: "", dueDate: ""});

    const handleSubmitNewTask = (e) => {
        e.preventDefault();
        transform(formData => ({
            body: formData.title || undefined,
            dueDate: formData.dueDate || undefined,
        }));
        post(`/projects/${project.id}/tasks`, {
            onSuccess: () => reset(),
        });
    };

    const handleToggleTaskIsDone = (task) => {
        router.patch(`/projects/${project.id}/tasks/${task.id}`, {
            isDone: !task.isDone
        })
    };

    const handleOnEditProject = () => {
        router.visit(`/projects/${project.id}/edit`)
    }

    const handleOnDeleteProject = () => {
        router.delete(`/projects/${project.id}`)
    }

    return (
        <AuthenticatedLayout auth={auth}>
            <Head title={project.title}/>


            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8" id="project">
                <Link href={'/projects'} as="a" className="text-indigo-600">Back</Link>
                <div className="flex justify-between items-center mt-8">
                    <h1 className="font-semibold text-2xl">{project.title}</h1>
                    <div className="flex gap-x-2">
                        <PrimaryButton onClick={handleOnEditProject}>Edit</PrimaryButton>
                        <DangerButton onClick={handleOnDeleteProject}>Delete</DangerButton>
                    </div>
                </div>
                <h2 className="mt-2">{project.description}</h2>


                <h3 className="mt-6 font-semibold text-lg">Add Task</h3>
                <form onSubmit={handleSubmitNewTask}>
                    <div>
                        <label htmlFor="title" className="block text-sm font-medium leading-6 text-gray-900">
                            Title
                        </label>
                        <div className="mt-2">
                            <input
                                value={data.title}
                                type="text"
                                name="title"
                                id="title"
                                required
                                className="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                placeholder="TODO app"
                                onChange={e => setData("title", e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="mt-4">
                        <label htmlFor="Due Date" className="block text-sm font-medium leading-6 text-gray-900">
                            Due Date
                        </label>
                        <div className="mt-2">
                            <input
                                value={data.dueDate}
                                type="date"
                                name="dueDate"
                                id="dueDate"
                                className="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                onChange={e => setData("dueDate", e.target.value)}
                            />
                        </div>
                    </div>
                    <InputError message={errors.message} className="mt-2"/>
                    <PrimaryButton className="mt-4" disabled={processing}>Create</PrimaryButton>
                </form>

                <div className="mt-8 divide-y divide-gray-200 border-t border-b border-gray-200">
                    {tasks.map((task, i) => (
                        <div key={i} className="relative flex items-start py-4 cursor-pointer"
                             onClick={() => handleToggleTaskIsDone(task)}>
                            <div className="min-w-0 flex-1 text-sm leading-6">
                                <label htmlFor={`task-${task.id}`} className="select-none font-medium text-gray-900">
                                    {task.body}
                                </label>
                                <p>
                                    {task.dueDate && new Date(task.dueDate).toISOString().slice(0, 10).replace(/-/g, "-")}
                                </p>
                            </div>
                            <div className="ml-3 flex h-6 items-center">
                                <input
                                    id={`task-${task.id}`}
                                    name={`task-${task.id}`}
                                    type="checkbox"
                                    className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                    checked={task.isDone}
                                />
                            </div>
                        </div>
                    ))}
                </div>
            </div>

        </AuthenticatedLayout>
    );
}

export default Project;
