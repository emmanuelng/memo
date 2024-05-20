import React, { useEffect, useState } from "react";
import MissingWordsQuestion from "./questions/MissingWordsQuestion";

const Game: React.FC<{ onEndGame: () => void; }> = (props) => {

    const [token, setToken] = useState<string>('');
    const [streak, setStreak] = useState<number>(0);
    const [isAnswerCorrect, setIsAnswerCorrect] = useState<boolean>(false);
    const [questionType, setQuestionType] = useState<string>('');
    const [question, setQuestion] = useState<any | null>(null);
    const [answer, setAnswer] = useState<any | null>(null);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onStartGame(), []);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onAnswerChanged(), [answer]);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onIsCorrectChanged(), [isAnswerCorrect]);

    const setState = (json: any) => {
        setToken(json.token);
        setIsAnswerCorrect(json.isCorrect === true);
        setStreak(json.streak);
        setQuestionType(json.questionType);
        setQuestion(json.question);
    };

    const onStartGame = () => {
        fetch(`api/game/start`, {
            method: 'POST'
        }).then(response => {
            response.json().then(json => { setState(json); });
        });
    };

    const onIsCorrectChanged = () => {
        if (isAnswerCorrect)
            setTimeout(onNextQuestion, 500);
    };

    const onAnswerChanged = () => {
        if (answer === null)
            return;
        
        fetch(`api/game/answer`, {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token, answer })
        }).then(response => {
            response.json().then(json => { setState(json); });
        });
    };

    const onNextQuestion = () => {
        fetch(`api/game/next`, {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token })
        }).then(response => {
            response.json().then(json => { setState(json); });
        });
    };

    const getQuestion = () => {
        switch (questionType) {
            case "MissingWordsQuestion":
                return <MissingWordsQuestion data={question} setAnswer={setAnswer} />;
            default:
                return <></>;
        }
    };

    return (
        <>
            <h1>Game</h1>
            <button onClick={props.onEndGame}>Retour au menu</button>
            <div>{getQuestion()}</div>
            <button onClick={onNextQuestion}>Passer</button>
            <div>Série en cours: {streak}</div>
        </>
    );
};

export default Game;