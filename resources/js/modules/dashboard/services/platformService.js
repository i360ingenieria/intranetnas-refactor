import api from "./api";

export async function getPlatforms() {
    const { data } = await api.get("/platforms");
    return data;
}