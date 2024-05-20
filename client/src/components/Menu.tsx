import React from "react";
import { ReactComponent as Logo } from '../logo.svg';

import './Menu.scss';

const Menu: React.FC<{ onStartGame: () => void; }> = (props) => {
    return (
        <div id="Menu">
            <div id="Logo">
                <Logo />                
                <div>Le jeu de mémorisation des versets</div>
            </div>
            <div id="Options">
                <button onClick={props.onStartGame}>Commencer une partie</button>
            </div>
        </div>
    );
};

export default Menu;