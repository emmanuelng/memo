import React, { useEffect, useState } from "react";
import { ReactComponent as HomeLogo } from './home.svg';
import { ReactComponent as Logo } from '../logo.svg';
import Question from "./questions/Question";

import './Game.scss';

const Game: React.FC<{ onEndGame: () => void; }> = (props) => {

    const SERVER_URL = !process.env.NODE_ENV || process.env.NODE_ENV === 'development' ? 'http://localhost/memo/' : '';

    const [token, setToken] = useState<string>('');
    const [streak, setStreak] = useState<number>(0);
    const [isAnswerCorrect, setIsAnswerCorrect] = useState<boolean>(false);
    const [questionType, setQuestionType] = useState<string>('');
    const [question, setQuestion] = useState<any | null>(null);
    const [canAnswer, setCanAnswer] = useState<boolean>(true);
    const [answer, setAnswer] = useState<any | null>(null);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onStartGame(), []);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onAnswerChanged(), [answer]);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onIsCorrectChanged(), [isAnswerCorrect]);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => onCanAnswerChanged(), [canAnswer]);

    const setState = (json: any) => {
        setToken(json.token);
        setIsAnswerCorrect(json.isCorrect === true);
        setStreak(json.streak);
        setQuestionType(json.questionType);
        setCanAnswer(json.canAnswer);
        setQuestion(json.question);
        setAnswer(null);
    };

    const onStartGame = () => {
        fetch(`${SERVER_URL}api/game/start`, {
            method: 'POST'
        }).then(response => {
            response.json().then(json => { setState(json); });
        });
    };

    const onIsCorrectChanged = () => {
        if (isAnswerCorrect)
            setTimeout(onNextQuestion, 1000);
    };

    const onCanAnswerChanged = () => {
        if (!canAnswer) {
            setTimeout(onNextQuestion, 1000);
        }
    };

    const onAnswerChanged = () => {
        if (answer === null)
            return;

        fetch(`${SERVER_URL}api/game/answer`, {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token, answer })
        }).then(response => {
            response.json().then(json => { setState(json); });
        });
    };

    const onNextQuestion = () => {
        fetch(`${SERVER_URL}api/game/next`, {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token })
        }).then(response => {
            response.json().then(json => { setState(json); });
        });
    };

    return (
        <>
            <div id="Header">
                <div id="HomeButton" >
                    <button onClick={props.onEndGame}><HomeLogo /></button>
                </div>
                <div id="Logo">
                    <Logo onClick={props.onEndGame} />
                </div>
            </div>
            <div id="Question">
                <Question type={questionType} data={question} setAnswer={setAnswer} />
            </div>
            <div id="Footer">
                <button onClick={onNextQuestion}>Passer</button>
                <div>Série en cours: {streak}</div>
            </div>
        </>
    );
};

export default Game;