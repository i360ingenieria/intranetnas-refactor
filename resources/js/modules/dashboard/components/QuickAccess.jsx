import { useEffect, useState } from "react";
import { getPlatforms } from "../services/platformService";
import QuickAccessCard from "./QuickAccessCard";
export default function QuickAccess() {
    const [platforms, setPlatforms] = useState([]);

    useEffect(() => {
        loadPlatforms();
    }, []);

    async function loadPlatforms() {
        try {
            const data = await getPlatforms();
            setPlatforms(data);
        } catch (error) {
            console.error(error);
        }
    }

    return (
        <div className="row">
            {platforms.map((platform) => (
                <QuickAccessCard
                    key={platform.id}
                    platform={platform}
                />
            ))}
        </div>
    );
}