import React, { useEffect, useState } from "react";

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

    const getVerseReference = () => {
        const book: string = props.data.reference.book;
        const firstVerse: any = props.data.reference.firstVerse;
        const lastVerse: any | null = props.data.reference.lastVerse;

        let referenceStr = `${book} `;

        if (!lastVerse) {
            referenceStr += `${firstVerse.chapter} : ${firstVerse.verse}`;
        } else if (firstVerse.chapter === lastVerse.chapter) {
            referenceStr += firstVerse.verse === lastVerse.verse
                ? `${firstVerse.chapter} : ${firstVerse.verse}`
                : `${firstVerse.chapter} : ${firstVerse.verse}-${lastVerse.verse}`;
        } else {
            referenceStr += `${firstVerse.chapter} : ${firstVerse.verse}`;
            referenceStr += ' - ';
            referenceStr += `${lastVerse.chapter} : ${lastVerse.verse}`;
        }

        return <span>{referenceStr}</span>;
    };

    return (
        <>
            <h2>Mots manquants</h2>
            <p>Trouvez les mots manquants dans ce verset.</p>
            <div>
                <div>{getVerseFragments()}</div>
                <div>{getVerseReference()}</div>
            </div>
            <div>
                <input type="text"
                    value={word}
                    onChange={e => setWord(e.target.value)}
                    autoFocus
                />
            </div>
        </>
    );
};

export default MissingWordsQuestion;