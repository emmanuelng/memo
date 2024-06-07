import React, { useEffect, useState } from "react";

import './MissingWordsQuestion.scss';
import Reference from "../commons/Reference";

const MissingWordsQuestion: React.FC<{
    data: any;
    setAnswer: (answer: any) => void;
}> = (props) => {

    const [word, setWord] = useState<string>('');
    const [nbFoundWords, setNbFoundWords] = useState<number>(0);

    const foundWords = props.data.fragments
        ?.filter((f: any) => f.type === 'word' && f.text != null)
        .map((f: any) => f.text);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onWordChanged(), [word]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onFoundWordsChanged(), [foundWords]);

    const onWordChanged = () => {
        props.setAnswer([...foundWords, word]);
    };

    const onFoundWordsChanged = () => {
        if (foundWords.length !== nbFoundWords) {
            setNbFoundWords(foundWords.length);
            setWord('');
        }
    };

    const getVerseFragments = () => {
        return (
            <>
                {props.data.fragments.map((fragment: any, i: number) => {
                    switch (fragment.type) {
                        case "word":
                            return fragment.text
                                ? <span key={i}>{fragment.text} </span>
                                : <span key={i}>{'_'.repeat(fragment.length)} </span>;
                        case "text":
                        default:
                            return <span key={i}>{fragment.text} </span>;
                    }
                })}
            </>
        );
    };

    return (
        <>
            <div id="Title">
                <h2>Mots manquants</h2>
                <p>Trouvez les mots manquants dans ce verset.</p>
            </div>
            <div id="Verse">
                <div id="Text">{getVerseFragments()}</div>
                <div id="Reference"><Reference data={props.data.reference}/></div>
            </div>
            <div id="Answer">
                <input type="text"
                    value={word}
                    placeholder="Tapez un mot..."
                    onChange={e => setWord(e.target.value)}
                    autoFocus
                />
                <div>
                    <small>Tapez le <strong>premier</strong> mot manquant du verset</small>
                </div>
            </div>
        </>
    );
};

export default MissingWordsQuestion;