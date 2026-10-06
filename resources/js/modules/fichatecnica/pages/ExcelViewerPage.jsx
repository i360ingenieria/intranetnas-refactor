import { useNavigate, useParams } from "react-router-dom";

import ExcelViewer from "../components/ExcelViewer";


export default function ExcelViewerPage() {

    const navigate = useNavigate();

    const { id } = useParams();


    function handleClose() {

        navigate("/fichatecnica");

    }


    return (

   <ExcelViewer
            fileId={id}
            onClose={handleClose}
        />

    );

}