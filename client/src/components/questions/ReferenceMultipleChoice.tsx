import React from "react";

import './ReferenceMultipleChoice.scss';
import Reference from "../commons/Reference";

const ReferenceMultipleChoice: React.FC<{
    data: any;
    setAnswer: (answer: any) => void;
}> = (props) => {
    return (
        <>
            <div id="Title">
                <h2>Trouvez la référence</h2>
                <p>Sélectionnez la référence de ce verset parmi les trois.</p>
            </div>
            <div id="Verse">
                {props.data.text}
            </div>
            <div id="Choices">
                {props.data.choices.map((choice: any, i: number) => (
                    <button
                        onClick={() => props.setAnswer(i)}
                        className={choice.isAnswer === null ? "" : choice.isAnswer === true ? "correct" : "incorrect"}
                    >
                        <Reference data={choice.reference} />
                    </button>
                ))}
            </div>
        </>
    );
};

export default ReferenceMultipleChoice;