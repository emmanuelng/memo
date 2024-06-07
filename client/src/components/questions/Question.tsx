import React from "react";
import MissingWordsQuestion from "./MissingWordsQuestion";
import ReferenceMultipleChoice from "./ReferenceMultipleChoice";

import './Question.scss';

const Question: React.FC<{
    type: string;
    data: any;
    setAnswer: (answer: any) => void;
}> = (props) => {
    switch (props.type) {
        case "MissingWordsQuestion":
            return <MissingWordsQuestion data={props.data} setAnswer={props.setAnswer} />;
        case "ReferenceMultipleChoice":
            return <ReferenceMultipleChoice data={props.data} setAnswer={props.setAnswer} />;
        default:
            return <></>;
    }
};

export default Question;
