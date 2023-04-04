import {usePage} from "@inertiajs/react";
import axios from "axios";
import {useEffect, useState} from "react";

export default function NotificationsBar() {
    const [notifications, setNotifications] = useState();

    useEffect(() => {
        fetchTasks();
    }, [])

    const fetchTasks = async () => {
        try {
            const response = await axios.get('/notifications');
            setNotifications(response.data);
        } catch (error) {
            console.error('Error fetching tasks:', error);
        }
    };

    console.log(notifications)

    return null;
};
