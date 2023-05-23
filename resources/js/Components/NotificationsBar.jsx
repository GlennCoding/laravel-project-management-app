import {usePage} from "@inertiajs/react";
import axios from "axios";
import {useEffect, useState} from "react";

export default function NotificationsBar() {
    const {notifications} = usePage().props

    console.log(notifications)

    const getEmoji = (type) => {
        switch (type) {
            case "TASK_COMPLETED":
                return "💪";
            case "TASK_OVERDUE":
                return "🖕";
            case "TASK_STREAK":
                return "🏆"
            default:
                return;
        }
    }

    return (
        <div className="bg-white w-full p-8">
            <ul className="space-y-2">
                {notifications && notifications.map((notification, i) => (
                    <li>
                        <span className="mr-1">{getEmoji(notification.type)}</span>
                        <span key={i}>{notification.message}</span>
                    </li>
                ))}
            </ul>
        </div>
    );
};
