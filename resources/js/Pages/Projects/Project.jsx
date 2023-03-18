import React from "react";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, router, useForm} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import InputError from "@/Components/InputError";

function Project({auth, project}) {
    const {data, setData, errors, post, processing, reset, transform} = useForm({title: "", dueDate: ""});

    const handleSubmit = (e) => {
        e.preventDefault();
        transform(formData => ({
            projectId: project.id,
            task: {
                title: formData.title,
                dueDate: formData ? formData.dueDate : undefined,
            },
        }));
        post('/tasks', {
            onSuccess: () => reset(),
            data: {
                projectId: project.id,
                task: {
                    title: data.title,
                    dueDate: data.dueDate
                }
            }
        });
    };

    const handleOnEdit = () => {
        router.visit(`/projects/${project.id}/edit`)
    }

    const handleOnDelete = () => {
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
                        <PrimaryButton onClick={handleOnEdit}>Edit</PrimaryButton>
                        <DangerButton onClick={handleOnDelete}>Delete</DangerButton>
                    </div>
                </div>
                <h2 className="mt-2">{project.description}</h2>


                <h3 className="mt-6 font-semibold text-lg">Add Task</h3>
                <form onSubmit={handleSubmit}>
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
                        <label htmlFor="Description" className="block text-sm font-medium leading-6 text-gray-900">
                            Description
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
            </div>

        </AuthenticatedLayout>
    );
}

export default Project;
