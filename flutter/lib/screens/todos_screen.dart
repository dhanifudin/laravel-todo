
import 'package:flutter/material.dart';
import 'package:todo/api/api_service.dart';

class TodosScreen extends StatefulWidget {
  const TodosScreen({super.key});

  @override
  State<TodosScreen> createState() => _TodosScreenState();
}

class _TodosScreenState extends State<TodosScreen> {
  final _apiService = ApiService();
  late Future<List<dynamic>> _todos;
  String? _token;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    _token = ModalRoute.of(context)!.settings.arguments as String?;
    if (_token != null) {
      _todos = _apiService.getTodos(_token!);
    }
  }

  void _addTodo() async {
    final titleController = TextEditingController();
    await showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Add Todo'),
          content: TextField(
            controller: titleController,
            decoration: const InputDecoration(labelText: 'Title'),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Cancel'),
            ),
            TextButton(
              onPressed: () async {
                if (_token != null) {
                  await _apiService.addTodo(_token!, titleController.text);
                  setState(() {
                    _todos = _apiService.getTodos(_token!);
                  });
                  Navigator.pop(context);
                }
              },
              child: const Text('Add'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Todos'),
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _todos,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            return ListView.builder(
              itemCount: snapshot.data!.length,
              itemBuilder: (context, index) {
                final todo = snapshot.data![index];
                return ListTile(
                  title: Text(todo['title']),
                  trailing: Checkbox(
                    value: todo['completed'],
                    onChanged: (value) {},
                  ),
                );
              },
            );
          } else if (snapshot.hasError) {
            return Center(
              child: Text('${snapshot.error}'),
            );
          }
          return const Center(
            child: CircularProgressIndicator(),
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _addTodo,
        child: const Icon(Icons.add),
      ),
    );
  }
}
