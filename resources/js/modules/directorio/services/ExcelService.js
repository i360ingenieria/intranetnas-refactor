 
export async function getExcel() {
    const { data } = await get("/directorio");
    return data;
}