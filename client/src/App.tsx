import { useState } from 'react';
import Menu from './components/Menu';
import Game from './components/Game';

import './App.scss';

function App(): JSX.Element {
    const [screen, setScreen] = useState<"menu" | "game">("menu");

    const onStartGame = () => {
        setScreen("game");
    };

    const onEndGame = () => {
        setScreen("menu");
    };

    const getScreen = () => {
        switch (screen) {
            case "game":
                return <Game onEndGame={onEndGame} />;
            case "menu":
            default:
                return <Menu onStartGame={onStartGame} />;
        }
    };

    return (
        <div className="App">
            {getScreen()}
        </div>
    );
}

export default App;
