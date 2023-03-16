import React from "react";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, router} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";

function Project({auth, project}) {
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
            </div>

        </AuthenticatedLayout>
    );
}

export default Project;
