/**
 * Created by m on 05.02.17.
 */

var MenuItem = new React.createClass({
    render: function () {
        return (
            <a href={this.props.url}>{this.props.title}</a>
        )
    }
});

var PostItem = new React.createClass({
    render: function () {
        return (
            <div >
                Почта: <a href={"mailto:" + this.props.email}>{this.props.email}</a>
            </div>
        )
    }
});

var PhoneItem = new React.createClass({
    render: function () {
        return (
            <div >
                Телефон: {this.props.phone}
            </div>
        )
    }
});

var MenuList = new React.createClass({
    render: function () {
        var nodes = this.props.items.map(function (item) {
            return (
                <li key={item.id} ><MenuItem url={item.url} title={item.title}/></li>
            )
        });
        return <ul>{nodes}</ul>
    }
});

var MenuSection = new React.createClass({
    getInitialState: function () {
        return {
            'menu': [],
            'email': "",
            "phone": "",
        }
    },

    componentDidMount: function () {
        var that = this;
        $.get(this.props.url).success(function (r) {
            that.setState(r);
        })
    },

    render: function () {
        return (
            <div>
                <MenuList items={this.state.menu}/>
                <hr />
                <div style={{padding: "1em"}}>
                    <PostItem email={this.state.email}/>
                    <PhoneItem phone={this.state.phone}/>
                </div>
            </div>
        )
    }
});

window.MenuSection = MenuSection;