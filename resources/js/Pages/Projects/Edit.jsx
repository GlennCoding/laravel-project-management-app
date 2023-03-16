import React from 'react';
import {Head, Link, useForm} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import InputError from "@/Components/InputError";

function Edit({auth, project}) {
    const {data, setData, errors, processing, put, reset} = useForm({
        title: project.title,
        description: project.description
    });

    console.log(data)

    const submit = (e) => {
        e.preventDefault();
        put(route(`projects.update`, {project}), {onSuccess: () => reset()});
    };

    return (
        <AuthenticatedLayout auth={auth}>
            <Head title={`Editing: ${project.title}`}/>


            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                <form onSubmit={submit} className="mt-6">
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
                               <textarea
                                   required
                                   value={data.description}
                                   placeholder="I'm going to build a sick TODO app with nice features."
                                   className="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                   onChange={e => setData("description", e.target.value)}
                               ></textarea>
                        </div>
                    </div>
                    <InputError message={errors.message} className="mt-2"/>
                    <PrimaryButton className="mt-4" processing={"processing"}>Submit</PrimaryButton>
                </form>
            </div>

        </AuthenticatedLayout>
    );
}

export default Edit;
