
import Card from "../components/Card";
import { geExcell }  from "../services/ExcelService";

export default function DataExcel() {
    const [directorio, setDirectorio] = useState([]);

    useEffect(() => {
        loadDirectorio();
    }, []);

    async function loadDirectorio() {
        try {
            const data = await geExcell();
            setDirectorio(data);
        } catch (error) {
            console.error(error);
        }
    }
    return (
     <div className="row">
            {directorio.map((usuario) => (
                <Card
                    key={usuario.id}
                    title={usuario.title}
                    description={usuario.description}
                    image={usuario.image}
                    onAction={() =>
                        alert(`Perfil: ${usuario.title}`)
                    }
                />
            ))}
        </div>
    );
}

