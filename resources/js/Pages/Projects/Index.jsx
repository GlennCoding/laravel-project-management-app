import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, useForm} from '@inertiajs/react';
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import DangerButton from "@/Components/DangerButton";

export default function Index({auth, projects}) {
    const {data, setData, errors, processing, post, reset} = useForm({title: "", description: ""});

    const submit = (e) => {
        e.preventDefault();
        post(route('projects.store'), {onSuccess: () => reset()});
    };

    return (
        <AuthenticatedLayout auth={auth}>
            <Head title="Index"/>

            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                <div>
                    <h1 className="font-semibold text-2xl">Create Project</h1>
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
                        <PrimaryButton className="mt-4" processing={"processing"}>Create</PrimaryButton>
                    </form>
                </div>

                <hr className="my-8"/>

                <div className="mt-6 space-y-2">
                    {projects.map((project, i) =>
                        <Link href={`/projects/${project.id}`} key={i} as="a"
                              className="bg-white shadow-sm rounded-lg p-4 w-full block hover:bg-gray-50">
                            <h1 className="text-xl font-semibold">
                                {project.title}
                            </h1>
                            <p>
                                {project.description}
                            </p>
                        </Link>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
